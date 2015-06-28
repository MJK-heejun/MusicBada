<?php

class User extends AppModel{

   //cakephp link up the name
   public $name = 'User';

    //public $hasMany = array(   	);

	public $validate = array(
		'username'=>array(
           'Not empty' => array(
			   'rule' => 'notEmpty',
			   'message' => 'Username can not be empty'
			),		
			'The username must be between 5 and 15 characters.'=>array(
				'rule'=>array('between', 5, 15),
				'message'=>'The username must be between 5 and 15 characters.'
			),
			'That username has already been taken'=>array(
				'rule'=>'isUnique',
				'message'=>'That username has already been taken.'
			),
		    'The username has to be alphanumeric'=>array(
				'rule'=>'alphaNumeric',
				'message'=>'Usernames must be letters and numbers only.'
			)
		),
        'password' => array(
		   //could be any name. yes, any name.
           'Not empty' => array(
			   'rule' => 'notEmpty',
			   'message' => 'Password can not be empty'
			),
		    //custom validation
			'Match Passwords'=>array(
				        //call whatever you want
				'rule'=>'matchPasswords', //the function declared below
				'message'=>'your passwords do not match'
			),
		    'The password has to be alphanumeric'=>array(
				'rule'=>'alphaNumeric',
				'message'=>'Password must be letters and numbers only.'
			),
			'The password must be between 5 and 15 characters.'=>array(
				'rule'=>array('between', 5, 15),
				'message'=>'The password must be between 5 and 15 characters.'
			),
		    '0 should not be the first letter of the password.'=>array(
				'rule'=>'noFirstLetterZero',
				'message'=>'the number 0 cannot be the first letter of the password'
			)
		), 
		'password_confirmation' => array(		
		   /*
           'Not empty' => array(
			   'rule' => 'notEmpty',
			   'message' => 'Please confirm your password'
			)*/			
		),
		'name' => array(		
           'Not empty' => array(
			   'rule' => 'notEmpty',
			   'message' => 'Name can not be empty'
			),
			'alphabets with space'=>array(
				'rule' => array('custom', '/^[a-z ]*$/i'),
				'message'=>'The name should be alphabets only.' 
			)						
		),		
		'email'=>array(
		    'Not empty' => array(
			   'rule' => 'notEmpty',
			   'message' => 'Email can NOT be empty'
			),
			'That email has already been taken'=>array(
				'rule'=>'isUnique',
				'message'=>'Email already exists'
			)
		)
	);


    //$data is the variable that is to be validated. 
    public function matchPasswords($data){ //$data is the argument passed from the password text box.
		if ($data['password'] == $this->data['User']['password_confirmation']){
			return true; //validation succeeded
		}else{ //if validation failed,
		   //make sure 'password_confirmation' display the invalidation message too.
		   $this->invalidate('password_confirmation', 'Your passwords do not match'); //invalidate message function
		   return false; 
		}
	}

    //don't allow '0' as the first letter of passwords. custom function that James made.
    public function noFirstLetterZero($data){
		if ($data['password'][0] == "0"){
		   return false;
		}else{
		   return true;
		}
	}


    //this gets called right before saving into database
	public function beforeSave($options = array()) { //$options=array() ~~ PHP 5.4 problem.
		//make sure it's isset in the data
	    if (isset($this->data['User']['password'])) {
			//reassign the data paasword as a hash value
	        $this->data['User']['password'] = AuthComponent::password($this->data['User']['password']);
	    }
	    return true;
	}



}

