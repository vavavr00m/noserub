<!--Force IE6 into quirks mode with this comment tag-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
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

<h1>CSS Left Frame Layout</h1>
<h3>Sample text here</h3>

</div>
</div>


<div id="maincontent">
<div class="innertube">

<?php echo $content_for_layout; ?>

</div>
</div>


</body>
</html>
