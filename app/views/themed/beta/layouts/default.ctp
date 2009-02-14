<?php
    $app_name = Configure::read('NoseRub.app_name');
    $headline = isset($headline) ? $headline : sprintf(__('Welcome to %s', true), $app_name);
    $title    = $app_name . ' - ' . $headline;
?>
<!--Force IE6 into quirks mode with this comment tag-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title; ?></title>

<!-- Meta Tags -->
	<?php echo $html->charset('UTF-8')?>
	<meta http-equiv="Content-Language" content="en" />
	<meta name="robots" content="all" />

	<link rel="Shortcut Icon" type="image/x-icon" href="<?php echo FULL_BASE_URL . Router::url('/'); ?>favicon.ico" />
	<?php echo $scripts_for_layout; ?>

<!-- CSS -->
	 <?php echo $this->element('css'); ?>
	                   
<!-- JavaScript -->
	<?php echo $this->element('javascript'); ?>
</head>

<body>

<div id="framecontent">
    <div class="innertube">
        <?php echo $noserub->widgetNavigation('main'); ?>
    </div>
</div>


<div id="maincontent">
    <div id="metanavi">
        <?php echo $noserub->widgetNavigation('meta'); ?>
    </div>
    
    <div class="innertube">
        <?php echo $content_for_layout; ?>
    </div>
</div>


</body>
</html>