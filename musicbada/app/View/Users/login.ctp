<h2>Please Login First</h2>
<?php
echo $this->Session->flash();
echo $this->Form->create('User');
echo $this->Form->input('username', array('label'=>'Username', 'autofocus' => 'on'));
echo $this->Form->input('password', array('label'=>'Password'));
echo $this->Form->end('Login'); //submit button

?>