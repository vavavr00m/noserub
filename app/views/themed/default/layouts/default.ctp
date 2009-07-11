<?php
    $app_name = Context::read('network.name');
    $headline = isset($headline) ? $headline : sprintf(__('Welcome to %s', true), $app_name);
    $title    = $app_name . ' - ' . $headline;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo Context::language(); ?>" lang="<?php echo Context::language(); ?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Language" content="<?php echo Context::language(); ?>" />
		<title><?php echo $title; ?></title>

        <?php echo $this->element('css'); ?>
	</head>
	<body id="top">
		<div id="hd">
		    <?php echo $html->link($html->image('logo.png', array('alt' => $app_name . ' - Logo')), '/', array('class' => 'logo'), false, false); ?>
			<?php echo $noserub->widgetNavigation('meta'); ?>
			<?php echo $noserub->widgetSearch(); ?>
		</div>
		<div id="bd">
			<div id="bd-inner">
				<div id="sidebar">
					<?php echo $noserub->widgetNavigation('main'); ?>
				    <div>
						<ul>
							<li>
								<a class="toggle" href="#">(-)</a>
								<a class="head" href="#">Invite People</a>
								<p>
									Let's build a better community together!
									<a href="#">Invite your co-workers, friends and classmates.</a>
								</p>
							</li>
						</ul>
					</div>
				</div>
                
				<?php echo $content_for_layout; ?>

			</div>
		</div>
		<div id="ft">
			<div>
				<h5>ThisNetworksName</h5>
				<ul>
					<li><a href="#">My Homepage</a></li>
					<li><a href="#">My Contacts</a></li>
					<li><a href="#">My Networks</a></li>
					<li><a href="#">Settings</a></li>
				</ul>
			</div><div>
				<h5>SomeMore</h5>
				<ul>
					<li><a href="#">This is a link</a></li>
					<li><a href="#">This is a link</a></li>
					<li><a href="#">This is a link</a></li>
					<li><a href="#">This is a link</a></li>
				</ul>
			</div><div>
				<h5>Legal</h5>
				<ul>
					<li><a href="#">User Agreement</a></li>
					<li><a href="#">Privacy Policy</a></li>
					<li><a href="#">Copyright Policy</a></li>
				</ul>
			</div>
		</div>
        <?php echo $this->element('javascript'); ?>
	</body>
</html>
