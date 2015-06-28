<?php



var_dump($filelist);

$c_val = 0;

echo "<div id='cMusic'>".$filelist[$c_val][1]."</div>";

echo "<div id='cMusica'>".$filelist[$c_val][0]."</div>";
?>


<audio id="all_player" controls>
	<!--
   <source src="<?php echo "/main/".iconv("UTF-8", "EUC-KR", "lissang아").".mp3"; ?>" type="audio/mpeg"></source>
-->

  	<source src=<?php echo "'".$basedir.$filelist[$c_val][0]."'"; ?> type="audio/mpeg">	  		  		  		  	  	
  		  		  		  		  	  	
Your browser does not support the audio element.
</audio> 

<p id="nextB" onclick="playNext();">
play next button
</p>	

<div>
<input id="random" type="checkbox" name="random" value="random" >play random<br/></input>
</div>

<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<?php
	//array 'mList' contains the music list of all available music
	echo "<script type='text/javascript'>var mList = new Array();</script>";
	echo "<script type='text/javascript'>";
	for($j = 0; $j<sizeof($filelist); $j++){
		echo "mList.push(['".$filelist[$j][0]."','".$filelist[$j][1]."']);";
	}
	echo "</script>";
?>

<script type="text/javascript">
//retrieve the current user's name
var user = <?php echo "'".$current_user['username']."'"; ?>;

var basedir = <?php echo "'".$basedir."'"; ?>;
</script>


<?php
	echo $this->Html->script('mypage/play');
?>



<?php

//$tag = id3_get_tag( "'".$basedir.$filelist[$c_val][0]."'" );
//print_r($tag);

//$results = read_mp3_tags("C:/wamp/www/main/app/webroot/upload/admin/music/newnew/01_ Let It Slide.mp3");
//print_r($results);

require_once('getid3/getid3.php');


// Initialize getID3 engine
$getID3 = new getID3;


// Analyze file and store returned data in $ThisFileInfo
$ThisFileInfo = $getID3->analyze($_SERVER['DOCUMENT_ROOT']."\..\musicbada_upload\adomin\music".$filelist[6][0]);
var_dump($ThisFileInfo);


/*
for($i = 0; $i < sizeof($filelist); $i++){
	$ThisFileInfo = $getID3->analyze("C:\wamp\musicbada_upload\adomin\music".$filelist[$i][0]);
	var_dump($ThisFileInfo);
}
*/

//echo $ThisFileInfo['id3v1']['album'];




?>