<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<h1>Startseite des Netzwerkes</h1>
	</div>
	<div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
	</div>
	<div id="bd-main-sidebar">
		<?php echo $noserub->widgetNewUsers(); ?>
		<?php echo $noserub->widgetPopularUsers(); ?>
		<?php echo $noserub->widgetLastActiveUsers(); ?>
		<?php echo $noserub->widgetLastLoggedInUsers(); ?>
	</div>
</div>
