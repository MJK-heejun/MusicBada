<?php
//importing the sanitze utility
App::uses('Sanitize', 'Utility');

class MainController extends AppController {

	public function index() {

	}




	public function beforeFilter(){
	   //call appcontroller, to not lose preconfiguration from the parent
	   parent::beforeFilter();

	   //non-logged in user have access to the followings...
	   $this->Auth->allow('index');
	}

	//determines what the logged in user have access to
    public function isAuthorized($user){
	   if($user['role'] == 'admin'){ //if admin, grant all access
	      return true;
	   }

	   return true; //allow the access
    }






}