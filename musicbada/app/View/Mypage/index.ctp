this is my page's index.ctp

<br/>

<?php
   echo $this->Html->link(
     'play music',
     array('controller' => 'mypage', 'action' => 'play')
   );
?>

<br/>

<?php
   echo $this->Html->link(
     'Upload and manage music',
     array('controller' => 'mypage', 'action' => 'manage')
   );
?>

<br/><br/><br/>

<?php
echo $this->Html->link('Logout ', array('controller' => 'users', 'action' => 'logout'));
?>