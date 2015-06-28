<?php

class Device extends AppModel{

   	//cakephp link up the name
	public $name = 'Device';


    public $belongsTo = array(
       'User' => array(
	      'classname' => 'User',
	      'foreignKey' => 'user_id'
	   )   
    );	


	public $validate = array(
		
	);


    //this gets called right before saving into database
	public function beforeSave($options = array()) { //$options=array() ~~ PHP 5.4 problem.

	    return true;
	}



}

