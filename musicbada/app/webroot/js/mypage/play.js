//parsing the 'Play All.json' file and save the data into 'mList' array
/* 
var mList = new Array();
$.getJSON('/main/upload/'+user+'/music_list/Play All.json', function(data){
	$.each(data, function(key, value){
		mList.push([value.path, value.name]);
	});	
});
*/

//tracker # for your current music
var cVal = 0; 

//set audio instance as now 'music'
var music = document.getElementById('all_player');

//play the audio instance
music.play();

//the music lists's length
var mLength = mList.length;
var realLength = mList.length;

music.addEventListener("paused",function(){
	alert('paused');	
});


music.addEventListener("ended",function(){
	
	playNext(); 	
	
});


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
	
	console.log(basedir+mList[cVal][0]);
	music.play();	
}



