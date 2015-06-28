
var username  = window.localStorage.getItem("musicbada_username");
var uuid      = window.localStorage.getItem("musicbada_uuid");
var token     = window.localStorage.getItem("musicbada_token");


//spinner initialization
$(document).on('pagebeforecreate', function(){     
    var interval = setInterval(function(){
        $.mobile.showPageLoadingMsg("a", "Loading...");
        clearInterval(interval);
    },1);    
});


$(document).bind('pagecreate', function(event, data){
	
	retrievePlayList();
		
    //$('#aaa').on("click", playMusic);	
	//prepareListener();
	

});




function retrievePlayList(){
	
	var myUrl = "http://localhost/main/WebServices/mJsonList/"+username+"/"+uuid+"/"+token;
	
	
	$.ajax({
		type: "GET",
        url: myUrl,
		datatype: 'json',
		beforeSend: function(){
			$.mobile.showPageLoadingMsg("a", "Loading...");
		},
		complete: function(){
			$.mobile.hidePageLoadingMsg();	
		},		
		success: function(data){
			$('#playList').html(""); //empty the 'ul' element before filling it in.	
			var parsed = $.parseJSON(data);														
            parsed.forEach(function (entry) {
				$('#playList').append("<li><a>"+entry.name+"</a></li>");
            });
			//refresh the list
			$('#playList').listview('refresh');
			//add click-event-listener onto the list items
			$('#playList a').on("click", function(e){
				window.localStorage.setItem('musicbada_currentList', $(e.target).text());
				$.mobile.changePage("listDetail.html", { transition: "slide"});	
			});				
		},
		error: function(jqXHR, exception){
			ajax_error_handling(jqXHR, exception);
		}
    });
}


function ajax_error_handling(jqXHR, exception){
	if (jqXHR.status === 0) {
		alert('Not connect.\n Verify Network.');
	} else if (jqXHR.status == 404) {
		alert('Requested page not found. [404]');
	} else if (jqXHR.status == 500) {
		alert('Internal Server Error [500].');
	} else if (exception === 'parsererror') {
		alert('Requested JSON parse failed.');
	} else if (exception === 'timeout') {
		alert('Time out error.');
	} else if (exception === 'abort') {
		alert('Ajax request aborted.');
	} else {
		alert('Uncaught Error.\n' + jqXHR.responseText);
	}		
}



/**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**/
/**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**/
/**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**/

//tracker # for your current music
var cVal = 0; 



//the music lists's length
var mLength;
var realLength;
var music;

function prepareListener(){
	//set audio instance as now 'music'
	music = document.getElementById('all_player');	
	
	music.addEventListener("paused",function(){
		alert('paused');	
	});

	music.addEventListener("ended",function(){	
		playNext(); 		
	});
}




var basedir = "http://localhost/main/WebServices/mPlay/"+username+"/"+uuid+"/"+token+"?path=";
var mList = new Array();

//temporary function for the first version
function playMusic(event){
	
	var myUrl = "http://localhost/main/WebServices/mJsonList/"+username+"/"+uuid+"/"+token+"/Play All";
	
	$.ajax({
		type: "GET",
        url: myUrl,
		datatype: 'jsonp',
		beforeSend: function(){
			$.mobile.showPageLoadingMsg("a", "Loading...");
		},
		complete: function(){
			$.mobile.hidePageLoadingMsg();	
		},		
		success: function(data){	
			var parsed = $.parseJSON(data);				
			$.each(parsed, function(key, value){
				mList.push([value.path, value.name]);
			});				
			
			//calculate and get the length
			mLength = mList.length;
			realLength = mList.length;
			
			//play the first music
			$('#cMusic').text(mList[cVal][1]);
			$('#all_player').attr('src', basedir+mList[0][0]);
			music.play();
		},
		error: function(jqXHR, exception){
			ajax_error_handling(jqXHR, exception);
		}
    });		
}




function playNext(){
	//basedir value is assigned in play.ctp
	//var basedir = "/main/mypage/test?path=";
		
	//if 'random' checkbox is selected,		
	if($('#random').is(':checked')){
		//generate random number b/w 0~(mLength-1)
		cVal = Math.floor(Math.random() * (mLength-1));
		$('#all_player').attr('src', basedir+mList[cVal][0]);
		$('#cMusic').text(mList[cVal][1]);
		
		//if no song left,		
		if((mLength-1) == 0){		
			//restore the length value	
			mLength = realLength;
		//if it's playing the music on the end of the list,			
		}else if(cVal == (mLength - 1)){			
			mLength--;					
		}else{					
			//swap position of the 'mList[cVal]' with the last value in the array. 
			var tmp = mList[cVal];
			mList[cVal] = mList[mLength - 1];
			mList[mLength - 1] = tmp;
			mLength--;	
		}
	//if 'random' checkbox is NOT selected,							
	}else{
		//restore the length value
		mLength = realLength;
		//if you reached the last music, go back to the first one
		if(cVal == (mLength - 1)){
			cVal = 0;			
		}else{
			cVal++;			
		}	

		$('#all_player').attr('src', basedir+mList[cVal][0]);
		$('#cMusic').text(mList[cVal][1]);
	}	
	
	music.play();	
}


