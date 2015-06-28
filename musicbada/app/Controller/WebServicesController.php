<?php
//importing the sanitze utility
App::uses('Sanitize', 'Utility');

class WebServicesController extends AppController {

  
    /*
	public function index() {
		 $this->redirect(array('controller' => 'Mypage', 'action' => 'manage'));
	}*/

  
	public function mLogin(){

		$this->layout = false;				
		header('Access-Control-Allow-Methods: GET');
		header('Access-Control-Allow-Origin: *');
		header('Content-type: application/json; charset=utf-8');

		if (!$this->request->is('GET')) {
			die("not correct method");
		}
			
		//private function for generating a random string
		function randomString($length){
  			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
  		  	$charLength = strlen($chars)-1;
			
			$randomString = "";
  		  	for($i = 0 ; $i < $length ; $i++){
  		    	$randomString .= $chars[mt_rand(0,$charLength)];
  		  	}
  		  	return $randomString;
		}
		
		$arr['auth'] = array();		
		
		//sanitizing
		$username = strip_tags($_GET['username']);
		$password = strip_tags($_GET['password']);		
		//$uuid = strip_tags($_GET['uuid']);
		$uuid = "android"; /*temporary. To be replaced by the previous commented line*/
		

		$this->loadModel('User');
		
		//if the $count is == 1, the user exists with correct password
		$count = $this->User->find('count', array(
		    'conditions' => array(
		    	'username' => $username,
		    	'password' => Security::hash($password, NULL, true)
			)
		 ));							
					
		//if the username and password are correct,
		if ($count == 1) {
						
			$id = $this->User->field('id', array('username' => $username));
			$status = $this->User->field('status', array('id' => $id));			
			$this->User->id = $id;
			
			$this->loadModel('Device');
			$device_id = $this->Device->field('id', array('user_id' => $id));
			$this->Device->id = $device_id;
			
			switch ($status) {
				case 'tmpBlocked':	
					$blockedTill = $this->User->field('blockedTill', array('id' => $id));
					$currentTime = date('Y-m-d H:i:s');
					
					//if the user is still within blocked period,
					if($currentTime < $blockedTill){
						$arr['auth'] = array('token' => "fail", "reason" => "The user is currently suspended");			
					//if the suspension period is elapsed					
					}else{						
						$this->User->saveField('status', 'normal');
						$this->User->saveField('loginAttemptCount', 0);
												
						//return token in json form
						//retrieve the value from the 'id' column of the user
						$this->request->data['Device']['user_id'] = $id;
			
						//create a token
						$token = randomString(20); //refer to the private function defined below
						$this->request->data['Device']['token'] = $token;
			
						$this->request->data['Device']['uuid'] = $uuid;
			
						//save uuid, user_id, token into the db ('Device' table)
						$this->Device->save($this->request->data);
			
						//return the token to the client in json form
						$arr['auth'] = array('token' => $token);			
					}								
					break;				
				case 'normal':
					$this->User->saveField('loginAttemptCount', 0);					
					
					//return token in json form		
					//retrieve the value from the 'id' column of the user
					$this->request->data['Device']['user_id'] = $id;
		
					//create a token
					$token = randomString(20); //refer to the private function defined below
					$this->request->data['Device']['token'] = $token;
					
					$this->request->data['Device']['uuid'] = $uuid;
		
					//save uuid, user_id, token into the db ('Device' table)
					$this->Device->save($this->request->data);
		
					//return the token to the client in json form
					$arr['auth'] = array('token' => $token);						
					break;				
				default:		
					$arr['auth'] = array('token' => 'fail', 'reason'=>'wassup bro');			
					break;
			}						
		//if username and password are incorrect,
		}else{								
			//if the $count is = 0, then the user does not exist
			$count = $this->User->find('count', array(
   			    'conditions' => array('username' => $username)
  				 ));
			//if the user exists,
			if($count > 0){ 
				$id = $this->User->field('id', array('username' => $username));
				$status = $this->User->field('status', array('id' => $id));
				
				
				switch ($status) {
					case 'normal':
						$loginAttemptCount = $this->User->field('loginAttemptCount', array('id' => $id));
						if($loginAttemptCount >= 4){
							//suspend the user
							$this->User->id = $id;
							$this->User->saveField('status', 'tmpBlocked');
								
							$blockedTill = time() + (1 * 6 * 60 * 60); //6 hours
							$blockedTill = date('Y-m-d H:i:s', $blockedTill);
													
							$this->User->saveField('blockedTill', $blockedTill);
							$arr['auth'] = array('token' => "fail", "reason" => "The user is suspended due to failed login attempts");
						}else{
							//keep track the number of login attempts the user made
							$this->User->id = $id;
							$this->User->saveField('loginAttemptCount', $loginAttemptCount+1);
							$arr['auth'] = array('token' => "fail", "reason" => "Wrong username or password");
						}							
						break;
					case 'tmpBlocked':
						$arr['auth'] = array('token' => "fail", "reason" => "The user is currently suspended");
						break;
					default:
						$arr['auth'] = array('token' => "fail", "reason" => "WTF OMG");
						break;
				}									
			}else{
				//else if the user does not exist,
				$arr['auth'] = array('token' => 'fail', 'reason' => 'The user does not exist');
			}				
		}
		
		echo json_encode($arr);
	}  


    public function play(){

		function security_procedure($basedir, $filename){
			//replace " with \
			$basedir = realpath(str_replace('"', '\\', $basedir));				
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
		$user_directory = "upload/../../../../../musicbada_upload/".$this->Auth->user('username')."/music";		
		$filename = $_GET["path"];

		//sanitizing
		$filename = strip_tags($filename);
		$filename = $user_directory.$filename;
		
		//basedir and security			
		//$mybasedir  = 'C:"wamp"musicbada_upload"'.$this->Auth->user('username').'"music';		
		$mybasedir  = $_SERVER['DOCUMENT_ROOT'].'".."musicbada_upload"'.$this->Auth->user('username').'"music';

		$path_parts = security_procedure($mybasedir, $filename);	
		$music_name = $path_parts['dirname']."/".$path_parts['basename'];

		
		header('Access-Control-Allow-Methods: GET');
    	header('Content-Type: audio/mpeg');		
    	header('Content-length: '.filesize($music_name));
		
		header('Content-Transfer-Encoding: binary');
 		header('Expires: 0');
 		header('Accept-Ranges: bytes'); //필요함. 프로그레스 바가 안눌러짐....늘어나지도 않고...
 		header('Etag: ' . $etag); //필요함. 프로그레스 바 눌를시 꺼지는 현상을 없애줌		
		header('Content-Disposition: inline; filename="'.$path_parts['basename'].'"'); //필요없는듯?
		header('X-Pad: avoid browser bug'); //필요없는듯?
		header('Cache-Control: no-cache'); //필요함. 시작하고 프로그레스 바를 클릭하면 멈추는 현상을 없애줌.
		
		readfile($music_name);
		
		//ob_clean();
		flush();
		exit;
    }


	public function mPlay($username = null, $uuid = null, $token = null){
		
		//*---Mobile Security---*//
		$username = strip_tags($username);		
		$uuid = strip_tags($uuid);
		$token = strip_tags($token);
	
		//if any of the argument is empty, fail		
		if($username == null || $uuid == null || $token == null){
			die("incorrect arguments");
		}
				
		$this->loadModel('Device');
		
		//if the $count is = 0, then the user does not exist
		$count = $this->Device->find('count', array(
	    	'conditions' => array(
	    		'User.username' => $username,
	    		'Device.uuid' => $uuid,
	    		'Device.token' => $token
			)
		));		
		//--*--- ---*--//
		
		//if such username,uuid,token combination do not exist, fail
		if($count != 1){
			die("wrong combination bro");
		}

		function security_procedure($basedir, $filename){
			//replace " with \
			$basedir = realpath(str_replace('"', '\\', $basedir));					
			$basedir_len = strlen($basedir);
	
			// compare the entered path with the basedir
			$path_parts = pathinfo($filename);
			$real_path  = realpath($path_parts['dirname']);
			if (substr($real_path, 0, $basedir_len) != $basedir) {
    			//appropriate action against crack-attempt
	    		die ('coding good - h4x1ng bad!');
			}
			//else
			return $path_parts;			
		}
										
		//SUCCESS2
		$this->layout = false;
		$user_directory = "upload/../../../../../musicbada_upload/".$username."/music";
		$filename = $_GET["path"];

		//sanitizing fail path
		$filename = strip_tags($filename);
		$filename = $user_directory.$filename;		
		
		//basedir and security					
		//$mybasedir  = 'C:"wamp"musicbada_upload"'.$username.'"music'; //this need to be changed after deployment
		$mybasedir  = $_SERVER['DOCUMENT_ROOT'].'".."musicbada_upload"'.$username.'"music';
		$path_parts = security_procedure($mybasedir, $filename);	
		$music_name = $path_parts['dirname']."/".$path_parts['basename'];
		
		//encoding?????
		$music_name = iconv('utf-8', 'euc-kr', $music_name);
		$parts = iconv('utf-8', 'euc-kr', $path_parts['basename']);
	
		ob_start();
		
		header('Access-Control-Allow-Methods: GET');
    	header('Content-Type: audio/mpeg');
		
    	header('Content-length: '.filesize($music_name));
		
		header('Content-Transfer-Encoding: binary');		
		header('Pragma: public');//필요한듯. For play '공성' 파일
 		header('Expires: 0');
 		header('Accept-Ranges: bytes'); //필요함. 프로그레스 바가 안눌러짐....늘어나지도 않고...
 		//header('Etag: ' . $etag); //필요함. 프로그레스 바 눌를시 꺼지는 현상을 없애줌		
		header('Content-Disposition: inline; filename="'.$parts.'"'); //필요없는듯?
		header('X-Pad: avoid browser bug'); //필요없는듯?
		header('Cache-Control: no-cache'); //필요함. 시작하고 프로그레스 바를 클릭하면 멈추는 현상을 없애줌.
		
		try {
    		ob_clean(); //필요한듯.....For playing "09 Losing my Mind.mp3" in Android Devices
		} catch (Exception $e) {
		}
		
		flush();
		
		readfile($music_name);
		exit;		
	}


	public function mJsonList($username = null, $uuid = null, $token = null, $listname = null){

		function file_list($dir_path, $fl, $myDirLen){
			$iterator = new FilesystemIterator($dir_path);
			foreach($iterator as $entry) {
	    		if ($entry->getType() == "file") {
	    			$filePath = $entry->getPath()."/".$entry->getFileName();
					$filePath = substr($filePath, $myDirLen);
					$fileName = $entry->getFileName();			
					array_push($fl, array($fileName));
	    		}
			}
			return $fl;
		}



		//*---Mobile Security---*//
		$username = strip_tags($username);		
		$uuid = strip_tags($uuid);
		$token = strip_tags($token);
	
		//if any of the argument is empty, fail		
		if($username == null || $uuid == null || $token == null){
			die("incorrect arguments");
		}
				
		$this->loadModel('Device');
		
		//if the $count is = 0, then the user does not exist
		$count = $this->Device->find('count', array(
	    	'conditions' => array(
	    		'User.username' => $username,
	    		'Device.uuid' => $uuid,
	    		'Device.token' => $token
			)
		));		
		
		
		//*--- ---*//
		//if such username,uuid,token combination do not exist, fail
		if($count != 1){
			die("wrong combination bro");
		}
		
		$this->layout = false;				
		header('Access-Control-Allow-Methods: GET');
		header('Access-Control-Allow-Origin: *');
		header('Content-type: application/json; charset=euc-kr');		
		
		$listname = strip_tags($listname);
		switch ($listname) {
			case null:
				
				//retrieve the list of all the files in $my_directory
				$filelist = array();
				$my_directory = "upload/../../../../../musicbada_upload/".$username."/music_list";
				$myDirLen = strlen($my_directory);		
				$filelist = file_list($my_directory, $filelist, $myDirLen);				
				
				array('token' => 'fail', 'reason' => 'The user does not exist');
				
				$arr = array();
				for($i = 0; $i<sizeof($filelist); $i++){
					$arr[$i] = array('name' => substr($filelist[$i][0], 0, -5));	
				}
				
				echo json_encode($arr);
				break;
			
			default:
				//$basedir = $_SERVER['DOCUMENT_ROOT']."../musicbada_upload/".$username."/music_list/"; //localhost only??????
				$basedir = $_SERVER['DOCUMENT_ROOT']."/../musicbada_upload/".$username."/music_list/";
				readfile($basedir.$listname.".json");				
				break;
		}
	}


	public function test(){
		
		$music_name = "lissang아.mp3";
		
		header('Access-Control-Allow-Methods: GET');
    	header('Content-Type: audio/mpeg');
    	header('Content-length: '.filesize($music_name));
/*
		header('Content-Transfer-Encoding: binary');
		header('Pragma: public');
 		header('Expires: 0');
 		header('Accept-Ranges: bytes'); //필요함. 프로그레스 바가 안눌러짐....늘어나지도 않고...
 		header('Etag: ' . $etag); //필요함. 프로그레스 바 눌를시 꺼지는 현상을 없애줌		
		header('Content-Disposition: inline; filename="'.$path_parts['basename'].'"'); //필요없는듯?
		header('X-Pad: avoid browser bug'); //필요없는듯?
		header('Cache-Control: no-cache'); //필요함. 시작하고 프로그레스 바를 클릭하면 멈추는 현상을 없애줌.
		*/
		
		
		//ob_clean();
		flush();
		
		readfile($music_name);
		exit;			
	}
	



	public function beforeFilter(){
	   //call appcontroller, to not lose preconfiguration from the parent
	   parent::beforeFilter();

	   //non-logged in user have access to the followings...
	   $this->Auth->allow('mLogin', 'mPlay', 'mJsonList', 'test');
	}

	//determines what the logged in user have access to
    public function isAuthorized($user){
	   if($user['role'] == 'admin'){ //if admin, grant all access
	      return true;
	   }

	   return true; //allow the access
    }






}