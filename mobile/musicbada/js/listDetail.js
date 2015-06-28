

//spinner initialization
$(document).on('pagebeforecreate', function(){     
    var interval = setInterval(function(){
        $.mobile.showPageLoadingMsg("a", "Loading...");
        clearInterval(interval);
    },1);    
});


$(document).bind('pagecreate', function(event, data){	
	musicListRetrieve();
	
	$('#playAll').on("click", function(e){
		playFromBeginning();
	});			
	
});



function musicListRetrieve(){
	
	var myUrl = "http://localhost/main/WebServices/mJsonList/"+username+"/"+uuid+"/"+token+"/Play All";
	
	$.ajax({
		type: "GET",
        url: myUrl,
		datatype: 'jsonp',
		beforeSend: function(){
		},
		complete: function(){
			$.mobile.hidePageLoadingMsg();	
		},		
		success: function(data){	
			$('#musicList').html(""); //empty the 'ul' element before filling it in.		
			var parsed = $.parseJSON(data);				
			$.each(parsed, function(key, value){	
				tmp_mList.push([value.path, value.name]);			
				$('#musicList').append("<li><a>"+value.name+"</a></li>");
			});				
			
			//refresh the list
			$('#musicList').listview('refresh');
			
			//add click-event-listener onto the list items
			/* NOT SUPPORTED FOR THE CURRENT VERSION *//*
			$('#musicList a').on("click", function(e){
				window.localStorage.setItem('musicbada_currentList', $(e.target).text());
				$.mobile.changePage("listDetail.html", { transition: "slide"});	
			});				
			*/						
		},
		error: function(jqXHR, exception){
			ajax_error_handling(jqXHR, exception);
		}
    });		
}



function playFromBeginning(){
	mList = tmp_mList; //assign tmp array to the real list
	tmp_mList = []; //empty the tmp array
	
	
	
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


