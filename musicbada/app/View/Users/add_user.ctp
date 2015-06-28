
<div id="add_form">

<div id="add_form_header">	
	<h2>Register new user</h2>
</div>	

<div id="add_form_body">
<?php   	
echo $this->Form->create('User'); 
?>

	<?php	    
		echo $this->Form->input('username', 
			array('label'=>'Username', 
				  'required'=>'TRUE',
				  'autofocus' => 'autofocus', 
				  'size' => '15', 
				  'maxlength' =>'15', 
				  'pattern' => '^[a-zA-Z0-9]{5,15}$',
				  'title' => 'Username must be alphanumeric 5~15 characters',
				  'error'=> array('attributes' => array('wrap' => 'span', 'class' => 'error-message'))));	
				  	
		echo $this->Form->input('password', 
			array('size' => '15', 
			      'maxlength' =>'15', 
			      'pattern' => '^[a-zA-Z0-9]{5,15}$',
			      'required'=>'TRUE', 
			      'error'=> array('attributes' => array('wrap' => 'span', 'class' => 'error-message'))));
				  
		echo $this->Form->input('password_confirmation', 
			array('type'=>'password', 
			      'size' =>'15', 
			      'maxlength' =>'15',
			      'pattern' => '^[a-zA-Z0-9]{5,15}$',
			      'required'=>'TRUE', 
			      'error'=> array('attributes' => array('wrap' => 'span', 'class' => 'error-message')))); //the type is password

		
		echo $this->Form->input('email', 
			array('type'=>'email', 
				  'required'=>'TRUE', 
				  'error'=> array('attributes' => array('wrap' => 'span', 'class' => 'error-message'))));
				
		//recaptcha
		require_once('recaptchalib.php');
		$publickey = "6LcGoOESAAAAAAN5MOYfyhXGfDDMzUIFo6C9Hryj"; // you got this from the signup page
		echo recaptcha_get_html($publickey);				  
	?>

<?php echo $this->Form->end('Submit');?>
</div>

</div>