<div id="mainIndex">
	<div id="left">
		<div>
			<?php
			/*
			echo $this->Html->image('test.jpg', array(
				'alt' => 'achung',
				'height' => '400',
				'width' => '400'
			));
			*/
			?>
		</div>
	</div>
	<div id="right">
		<div id="loginPanel">
			<?php
			echo $this->Form->create('User', array('controller'=>'Users', 'action' => 'login'));
			echo $this->Form->input('username', array('label'=>'Username', 'autofocus' => 'on'));
			echo $this->Form->input('password', array('label'=>'Password'));
			echo $this->Form->end('Login'); //submit button
			?>
			<?php
		    echo $this->Html->link(
     			'Register',
	     		array('controller' => 'users', 'action' => 'addUser')
   			);
			?>			
		</div>
		<div id="sns">
			Copyright & SNS
		</div>
	</div>
</div>


<?php
echo $this->Html->css('main/index');
?>