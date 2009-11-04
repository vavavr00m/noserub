<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Language" content="en" />
		<title>NoseRub - System Update</title>

        <?php echo $this->element('css'); ?>
	</head>
	<body id="top">
		<div id="hd">
	    <?php echo $html->link($html->image('logo.png', array('alt' => 'Logo')), '/', array('class' => 'logo', 'escape' => false)); ?>
		</div>
		<div id="bd">
			<div id="bd-inner">
				<div id="sidebar">
				</div>
				<div id="bd-main">
                    <?php echo $content_for_layout; ?>
				</div>
			</div>
		</div>
		<div id="ft">
		</div>
        <?php echo $this->element('javascript'); ?>
	</body>
</html>