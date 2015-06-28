
<?php echo $this->Html->docType('html5'); ?>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title><?php echo $title_for_layout; ?></title>
	<?php
		//echo $this->Html->meta('icon');

		
		echo $this->Html->css('cake.generic');
				
		//echo $this->Html->charset('EUC-KR');
		//echo $this->fetch('meta');
		//echo $this->fetch('css');
		//echo $this->fetch('script');
	?>
</head>
<body>
	<div id="container">
		<div id="header">			
		</div>
		<div id="content">
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">			
		</div>
	</div>
	<?php
		//debugging purpose: SQL log 
		echo $this->element('sql_dump'); 
	?>
</body>
</html>
