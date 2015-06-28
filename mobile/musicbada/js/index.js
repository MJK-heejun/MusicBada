
var cVal = 0;
var mList = new Array();
var tmp_mList = new Array();
var mLength;
var theMedia;








$(document).bind('pageinit', function(event, data){
	$("#loginButton").click(login);	
});



function login(){
	
	//var myUrl = "http://localhost/main/WebServices/mLogin";
	var myUrl = "http://localhost/musicbada/WebServices/mLogin";
	
	
	$.ajax({
		type: "GET",
        url: myUrl,
        data: $("#loginForm").serialize(), // serializes the form's elements.
		datatype: 'json',
		beforeSend: function(){
			$.mobile.showPageLoadingMsg("a", "Loading...");
			$("#loginForm").serialize();			
		},
		complete: function(){
			$.mobile.hidePageLoadingMsg();				
		},		
		success: function(data){
			var parsed = $.parseJSON(data);
			//save the token value into the local WEB storage (HTML5 specification)
			if(parsed.auth['token'] == 'fail'){
				alert("Failed: "+parsed.auth['reason']);
			}else{
				window.localStorage.setItem('musicbada_username', $('#username').val());
				window.localStorage.setItem('musicbada_uuid', 'android');
				window.localStorage.setItem('musicbada_token', parsed.auth['token']);
				//redirect to main.html
				$.mobile.changePage("main.html", { transition: "slide"});								
			}
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
