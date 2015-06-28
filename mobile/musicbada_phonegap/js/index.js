var cVal = 0;
var mList = new Array();
var tmp_mList = new Array();
var mLength;
var theMedia = null;
var myBaseUrl = "http://192.168.1.87";
var listDetailPlayUrl;



var app = {
    // Application Constructor
    initialize: function() {
        this.bindEvents();
    },
    // Bind Event Listeners
    //
    // Bind any events that are required on startup. Common events are:
    // 'load', 'deviceready', 'offline', and 'online'.
    bindEvents: function() {
        document.addEventListener('deviceready', this.onDeviceReady, false);
    },
    onDeviceReady: function() {
        app.receivedEvent('deviceready');
    },
    receivedEvent: function(id) {


        console.log('Received Event: ' + id);       
        //add click-event-listener to the login button
        $("#loginButton").click(login);	

    }
};



function login(){
	
	var myUrl = myBaseUrl+"/main/WebServices/mLogin";
		
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


/*********************************************/
/*********************************************/

function playFromBeginning(){
	mList = tmp_mList; //assign tmp array to the real list
	tmp_mList = []; //empty the tmp array
	
	console.log("****yoink:"+listDetailPlayUrl+mList[0][0]);
	
	theMedia = new Media(listDetailPlayUrl+mList[0][0], onSuccess, onError);	
	cVal = 0;
	theMedia.play();
}

function onSuccess(){
	playNext();
}

function onError(e){
	console.log(e);
	playNext();
}

function playNext(){
	theMedia.release();
	if(cVal == mList.length - 1)
		cVal = 0;
	else if(cVal < mList.length - 1)
		cVal++;
	else if(cVal > mList.length - 1)
		cVal = 0; //Something screwed up, so just set the cVal as '0'
	
	theMedia = new Media(listDetailPlayUrl+mList[0][0], onSuccess, onError);
	theMedia.play();
}

