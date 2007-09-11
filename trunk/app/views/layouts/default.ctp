<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>NoseRub</title>

<!-- Meta Tags -->
	<?php echo $html->charset('UTF-8')?>
	<meta http-equiv="Content-Language" content="en" />
	<meta name="robots" content="all" />

	<link rel="Shortcut Icon" type="image/x-icon" href="<?php echo NOSERUB_FULL_PATH; ?>favicon.ico" />
	<?php echo $scripts_for_layout; ?>

<!-- CSS -->
	 <?php echo $this->renderElement('css'); ?>
	                   
<!-- JavaScript -->
	<?php echo $this->renderElement('javascript'); ?>
       
    </head>
	<body class="jamal {controller:'<?php echo $this->name; ?>',action:'<?php echo $this->action; ?>'<?php echo ($session->check('User')?',session:true':''); ?>}">
	<div id="top"></div>
	<?php echo $this->renderElement('metanav'); ?>

		<div id="header" class="wrapper">
			<div id="logo">
				<h1><a title="NoseRub" href="/">NoseRub</a></h1>
	  		</div>
	  	
	  			<?php echo $this->renderElement('mainnav'); ?>
		</div>
		
		<br class="clear" />
		
		<div id="headline">
			<div class="wrapper">
				<h2><?php echo isset($headline) ? $headline : 'Welcome to NoseRub'; ?></h2>
			</div>
		</div>
		<?php echo $this->renderElement('subnav'); ?>
		
		<div id="content" class="wrapper">
            <?php echo $content_for_layout?>
		</div>

		<div id="footer" class="wrapper">
			<p><a href="http://noserub.com"><img src="/images/footer/noserub-logo.gif" class="noserublogo" /></a> Powered by <a href="http://noserub.com">NoseRub</a></p>
		</div>

</body>
</html>