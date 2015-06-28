//tracker # for your current music
var cVal = 0; 

//set audio instance as now 'music'
var music = document.getElementById('all_player');

//the music lists's length
var mLength;
var realLength;

var mList = new Array();
//parsing the 'Play All.json' file and save the data into 'mList' array	
$.ajax({
    type: "GET",
    url: "http://localhost/musicbada/play2.php?callback=?",
	//url: "http://www.musicbada.com/musicbada/musicbada/play2.php",
    contentType: "application/json; charset=utf-8",
    dataType: "jsonp",
    success: function(data) {
		$.each(data, function(key, value){
			mList.push([value.path, value.name]);				
		});	
		post_load();
    },
    error: function (xhr, textStatus, errorThrown) {
        console.log(xhr.responseText);
    }
});

function post_load(){
	
	mLength = mList.length;
	realLength = mList.length;
	
	//play the audio instance
	music.play();
}


music.addEventListener("paused",function(){
	alert('paused');	
});


music.addEventListener("ended",function(){
	 	
	//if 'random' checkbox is selected,		
	if($('#random').is(':checked')){
		//generate random number b/w 0~(mLength-1)
		cVal = Math.floor(Math.random() * (mLength-1));
		$('#all_player').attr('src', '/main/'+mList[cVal][0]);
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

		$('#all_player').attr('src', 'http://localhost/main/'+mList[cVal][0]);
		$('#cMusic').text(mList[cVal][1]);
	}	
	this.play();
});

