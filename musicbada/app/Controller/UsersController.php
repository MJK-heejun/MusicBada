<?php
//importing the sanitze utility
App::uses('Sanitize', 'Utility');

class UsersController extends AppController {
	public function index() {

	}


    public function addUser(){
		//$this->loadModel('User');

		if ($this->request->is('post')) {
			//strip tags
			$this->request->data['User']['username'] = Sanitize::html($this->request->data['User']['username'], array('remove' => 'true'));
			$this->request->data['User']['password'] = Sanitize::html($this->request->data['User']['password'], array('remove' => 'true'));
			$this->request->data['User']['email'] = Sanitize::html($this->request->data['User']['email'], array('remove' => 'true'));

			$this->request->data['User']['role'] = 'normal';
			
			//recaptcha
			require_once('recaptchalib.php');
  			$privatekey = "6LcGoOESAAAAAF71cgFWLU7n5zslL5p-VpNkO5Tf";
  			$resp = recaptcha_check_answer ($privatekey,
                                			$_SERVER["REMOTE_ADDR"],
                                			$_POST["recaptcha_challenge_field"],
                                			$_POST["recaptcha_response_field"]);
			
			//you need this line for '$this->User->validates()' 														
			$this->User->set($this->request->data);		
			 
			if ($this->User->validates() && $resp->is_valid) {
				//save the data
				$this->User->save($this->request->data);
				
                $this->Session->setFlash('The user has been saved');

				
				//create folder if it doesn't exist
				mkdir($_SERVER['DOCUMENT_ROOT'].'/../musicbada_upload/'.$this->request->data['User']['username'].'/music', 0755, true);
				mkdir($_SERVER['DOCUMENT_ROOT'].'/../musicbada_upload/'.$this->request->data['User']['username'].'/music_list', 0755, true);			

				$this->redirect(array('controller'=>'main', 'action' => 'index'));
			} else {
				if(!$resp->is_valid){
					$this->Session->setFlash('The user could not be saved. Please, try again.: You typed in the wrong security code');
				}else{
					$this->Session->setFlash('The user could not be saved. Please, try again.');	
				}				
			}
		}
	}


	public function login(){
		if ($this->request->is('post')){
				
			//strip tags
			$this->request->data['User']['username'] = Sanitize::html($this->request->data['User']['username'], array('remove' => 'true'));
			$this->request->data['User']['password'] = Sanitize::html($this->request->data['User']['password'], array('remove' => 'true'));
			
			$id = $this->User->field('id', array('username' => $this->request->data['User']['username']));
			$status = $this->User->field('status', array('id' => $id));
						
			if($status == 'tmpBlocked'){
				
				$blockedTill = $this->User->field('blockedTill', array('id' => $id));
				$currentTime = date('Y-m-d H:i:s');
				
				//if the user is still blocked,
				if($currentTime < $blockedTill){
					$this->Session->setFlash("You are blocked till ".$blockedTill." due to continuous failed login attempt");
					
				//if the suspension period is elapsed					
				}else{
					//attempt to login the user
		   			if($this->Auth->login()){ //if login was successful
		   				$this->User->id = $id;
						$this->User->saveField('status', 'normal');
						$this->User->saveField('loginAttemptCount', 0);
				   		$this->redirect(array('controller' => 'mypage', 'action' => 'index'));
		   			}else{//if login was not successful
		   				$this->User->id = $id;
						$this->User->saveField('status', 'normal');
						$this->User->saveField('loginAttemptCount', 1);						
				   		$this->Session->setFlash("your username/pass combination incorrect!!!!");
		   			}						
				}			
									
			}else if($status == 'normal'){
		   		//attempt to login the user
		   		if($this->Auth->login()){ //if login was successful
		   			$this->User->id = $id;
					$this->User->saveField('loginAttemptCount', 0);				
				   	$this->redirect(array('controller' => 'mypage', 'action' => 'index'));
		   		}else{//if login was not successful
		   			
		   			$loginAttemptCount = $this->User->field('loginAttemptCount', array('id' => $id));
					
					if($loginAttemptCount >= 4){
						//suspend the user
						$this->User->id = $id;
						$this->User->saveField('status', 'tmpBlocked');
						
						$blockedTill = time() + (1 * 6 * 60 * 60); //6 hours
						$blockedTill = date('Y-m-d H:i:s', $blockedTill);
												
						$this->User->saveField('blockedTill', $blockedTill);
						$this->Session->setFlash("You are blocked till ".$blockedTill." due to continuous failed login attempt");
					}else{
						//keep track the number of login attempts the user made
						$this->User->id = $id;
						$this->User->saveField('loginAttemptCount', $loginAttemptCount+1);
						$this->Session->setFlash("your username/pass combination incorrect!!!!");
					}
		   		}				
			}else{//the user does not exist
				$this->Session->setFlash("your username/pass combination incorrect!!!!");
			}	   	
		}	
	}

    public function logout(){
		//destory the session and redirect
		$this->redirect($this->Auth->logout());
	}


	public function beforeFilter(){
	   //call appcontroller, to not lose preconfiguration from the parent
	   parent::beforeFilter();

	   //non-logged in user have access to the followings...
	   $this->Auth->allow('index', 'addUser');
	}

	//determines what the logged in user have access to
    public function isAuthorized($user){
	   if($user['role'] == 'admin'){ //if admin, grant all access
	      return true;
	   }

	   return true; //allow the access
    }






}