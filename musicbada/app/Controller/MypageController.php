<?php
//importing the sanitze utility
App::uses('Sanitize', 'Utility');

class MypageController extends AppController {

	public function index() {



	}

	public function play() {
			
		function file_list($dir_path, $fl, $myDirLen){
			$iterator = new FilesystemIterator($dir_path);
			foreach($iterator as $entry) {
	    		if ($entry->getType() == "file") {
	    			$filePath = $entry->getPath()."/".$entry->getFileName();
					$filePath = substr($filePath, $myDirLen);
					$fileName = $entry->getFileName();			
					array_push($fl, array($filePath, $fileName));
	    		}elseif($entry->getType() == "dir"){
					$fl = array_merge($fl, file_list($dir_path."/".$entry->getFileName(), array(), $myDirLen));
				}
			}
			return $fl;
		}

		//retrieve the list of all the files in $my_directory
		$filelist = array();
		$my_directory = "upload/../../../../../musicbada_upload/".$this->Auth->user('username')."/music";
		$myDirLen = strlen($my_directory);		
		$filelist = file_list($my_directory, $filelist, $myDirLen);
		
		$project_name = Configure::read('project_name'); //global variable defined in AppController.php
		//$this->set('basedir', "/main/mypage/test?path=".$my_directory);
		$this->set('basedir', "/".$project_name."/WebServices/play?path=");
		$this->set('filelist', $filelist);
	
		
		//create .json file
		$myJsonFile =$_SERVER['DOCUMENT_ROOT']."/../musicbada_upload/".$this->Auth->user('username')."/music_list/Play All.json";
		$fh = fopen($myJsonFile, 'w') or die("can't open file");
		
		$myContent = "";
		$l_filelist = sizeof($filelist); 
		for($i = 0; $i< $l_filelist; $i++){
			$myContent = $myContent.'';
			if($i == 0){
				$myContent = $myContent."
{	
	\"".$i."\": ";	
				$myContent = $myContent."
	{
		\"path\": \"".$filelist[$i][0]."\", 
		\"name\": \"".$filelist[$i][1]."\"  
	}";
				//if there are more than 1 music, add comma: ','
				if($l_filelist != 1){
					$myContent = $myContent.",";
				}else{
					$myContent = $myContent."	
}";							
				}
			}elseif($i == ($l_filelist - 1)){
				$myContent = $myContent."				
	\"".$i."\": ";
				$myContent = $myContent."
	{
		\"path\": \"".$filelist[$i][0]."\", 
		\"name\": \"".$filelist[$i][1]."\"  
	}";
				$myContent = $myContent."
	
}";							
			}else{
				$myContent = $myContent."				
	\"".$i."\": ";				
				$myContent = $myContent."
	{
		\"path\": \"".$filelist[$i][0]."\", 
		\"name\": \"".$filelist[$i][1]."\"  
	},";
			}
		}		
		

		
		//encoding issue
		//June 2, edited to comment out the iconv. Commented out to read json file properly
		//$myContent = iconv("EUC-KR","UTF-8", $myContent);
		
		fwrite($fh, $myContent);
		fclose($fh);	
	
	}

	public function manage() {

	}

    public function test(){
    	/*//SUCCESS
    	$this->layout = false;

        $filename = Sanitize::html($_GET["path"], array('remove' => 'true'));
		//$filename = "Blurred.mp3";
		
		header('Access-Control-Allow-Methods: GET');
		header('Access-Control-Allow-Origin: asfasf');
    	header('Content-Type: audio/mpeg');
    	header('Content-length: '.filesize($filename));
		readfile($filename);
		*/
		
		function security_procedure($basedir, $filename){
			//replace " with \
			$basedir = str_replace('"', '\\', $basedir);				
			$basedir_len = strlen($basedir);
	
			// compare the entered path with the basedir
			$path_parts = pathinfo($filename);
			$real_path  = realpath($path_parts['dirname']);
			if (substr($real_path, 0, $basedir_len) != $basedir) {
    			/* appropriate action against crack-attempt*/
	    		die ('coding good - h4x1ng bad!');
			}
			//else
			return $path_parts;			
		}
		
		//SUCCESS2
		$this->layout = false;
		$filename = $_GET["path"];

		//sanitizing
		$filename = strip_tags($filename);
		
		//basedir and security			
		$mybasedir  = 'C:"wamp"musicbada_upload"'.$this->Auth->user('username').'"music';
		$path_parts = security_procedure($mybasedir, $filename);	
		$music_name = $path_parts['dirname']."/".$path_parts['basename'];

		
		header('Access-Control-Allow-Methods: GET');
    	header('Content-Type: audio/mpeg');
    	header('Content-length: '.filesize($music_name));
		readfile($music_name);

    }
	
    public function testtt(){
    	$this->layout = false;

        $filename = Sanitize::html($_GET["path"], array('remove' => 'true'));
		//$filename = "Blurred.mp3";
		
		header('Access-Control-Allow-Methods: GET');
    	header('Content-Type: audio/mpeg');
    	header('Content-length: '.filesize($filename));
		readfile($filename);
    }

	public function beforeFilter(){
	   //call appcontroller, to not lose preconfiguration from the parent
	   parent::beforeFilter();

	   //non-logged in user have access to the followings...
	   $this->Auth->allow('test1','test2');
	}

	//determines what the logged in user have access to
    public function isAuthorized($user){
	   if($user['role'] == 'admin'){ //if admin, grant all access
	      return true;
	   }

	   return true; //allow the access
    }






}