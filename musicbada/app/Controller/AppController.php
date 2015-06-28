<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	
	public $components = array( //make as array for multiple components
	   'Session', //so we can display flash message
	   'Auth' => array( //set auth options
	       //location the user get redirected when logined(which is index)
		   'loginRedirect' =>array('controller' => 'Main', 'action'=>'index'),
           //logout redirect
		   'logoutRedirect' =>array('controller' => 'Main', 'action'=>'index'),
		   //error message of authorization
		   'authError' => "Access denied",
		   //tells the auth component where the authorization will occur
		   'authorize' => array('Controller')
		) 	
	);
    
	
	//determines what the logged in user have access to
	public function isAuthorized($user){ //takes in the current user as argument	   
	   //for now, if you login, give them all authorization	   
	   return true; 
	}
 
    //before any actions, this gets run
    public function beforeFilter(){
       //determines what non-logged in user have access to    
       //$this->Auth->allow(array('controller' => 'Users', 'action' => 'index'));	//allow non-logged in users to view index and view
 
       //declare logged_in var ~ loggedIn() returns true if logged in.
       $this->set('logged_in', $this->Auth->loggedIn()); 
       
	   //send the information about the entire user to the view, and it's gonna be stored in the 'current_user' variable
	   $this->set('current_user', $this->Auth->user());
       session_set_cookie_params(0);	


       Configure::write('project_name', 'musicbada'); //global variable for controller files

	}
	
	
	
}
