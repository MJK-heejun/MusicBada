/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
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
    // deviceready Event Handler
    //
    // The scope of 'this' is the event. In order to call the 'receivedEvent'
    // function, we must explicity call 'app.receivedEvent(...);'
    onDeviceReady: function() {
        app.receivedEvent('deviceready');
    },
    // Update DOM on a Received Event
    receivedEvent: function(id) {
        var parentElement = document.getElementById(id);
        var listeningElement = parentElement.querySelector('.listening');
        var receivedElement = parentElement.querySelector('.received');

        listeningElement.setAttribute('style', 'display:none;');
        receivedElement.setAttribute('style', 'display:block;');

        console.log('Received Event: ' + id);        
        
        //mediaLoad();
        prepareListener();
    }
};


var theMedia = null;
function mediaLoad(){
	console.log("mediaLoad function started");
	theMedia = new Media("http://robtowns.com/music/blind_willie.mp3", onSuccess, onError);
	//theMedia = new Media("http://www.musicbada.com/test/playSanctus.php", onSuccess, onError);
    theMedia.play();	
}
function onSuccess(){        
	console.log("onSuccess function started");

}
function onError(e){
	console.log("error occured: '"+e+"'");
}


/**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**/
/**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**/
/**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**/


var username  = "adomin";
var uuid      = "android";
var token     = "zBmVPDdpoSVWfDLDHO63";



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




var basedir = "http://192.168.1.87/main/WebServices/mPlay/"+username+"/"+uuid+"/"+token+"?path=";
var mList = new Array();

//temporary function for the first version
function playMusic(event){
	
	var myUrl = "http://192.168.1.87/main/WebServices/mJsonList/"+username+"/"+uuid+"/"+token+"/Play All";
	
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

