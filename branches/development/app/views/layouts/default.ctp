<?php
    $app_name = defined('NOSERUB_APP_NAME') ? NOSERUB_APP_NAME : 'NoseRub';
    $headline = isset($headline) ? $headline : 'Welcome to ' . $app_name;
    $title    = $app_name . ' - ' . $headline;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>
	    <?php echo $title; ?>
	</title>

<!-- Meta Tags -->
	<?php echo $html->charset('UTF-8')?>
	<meta http-equiv="Content-Language" content="en" />
	<meta name="robots" content="all" />

	<link rel="Shortcut Icon" type="image/x-icon" href="<?php echo FULL_BASE_URL . Router::url('/'); ?>favicon.ico" />
	<?php echo $scripts_for_layout; ?>



    <?php  if (is_a($mainMenu->getActiveMenuItem(), 'SocialStreamMenuItem')) { ?>
        <!-- RSS -->
        <?php
            $filter = $filter == '' ? 'all' : $filter;
        ?>
        <link rel="alternate" type="application/rss+xml" title="Social Stream Feed" href="<?php echo Router::Url('/social_stream/' . $filter . '/rss'); ?>" />
	<?php } ?>

<!-- CSS -->
	 <?php echo $this->element('css'); ?>
	                   
<!-- JavaScript -->
	<?php echo $this->element('javascript'); ?>
       
    </head>
	<body>
<!-- Revision $Revision$ -->
	<div id="top"></div>
	<?php echo $this->element('metanav'); ?>

		<div id="header" class="wrapper">
			<div id="logo">
				<h1><a title="<?php echo $app_name; ?>" href="/"><?php echo $app_name; ?></a></h1>
	  		</div>
	  	
	  			<?php echo $this->element('mainnav'); ?>
		</div>
	
		<div id="headline">
			<div class="wrapper">
				<h2><?php echo $headline; ?></h2>
			</div>
		</div>
		<?php 
			if (is_a($mainMenu->getActiveMenuItem(), 'SettingsMenuItem')) {
				echo $this->element('subnav');
		    } 
		?>
		
		<div id="content" class="wrapper">
            <?php echo $content_for_layout; ?>
		</div>

		<div id="footer" class="wrapper">
			<p><a href="http://noserub.com"><img src="<?php echo Router::url('/images/footer/noserub-logo.gif'); ?>" class="noserublogo" alt="NoseRub Logo" /></a> Powered by <a title="Decentralized social networks" href="http://noserub.com">NoseRub</a></p>
		</div>

</body>
</html>