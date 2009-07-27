<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<h1>Startseite des Netzwerkes</h1>
	</div>
	<div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
	</div>
	<div id="bd-main-sidebar">
        <?php echo $noserub->widgetLastEvents(); ?>
        <?php echo $noserub->widgetAd('200x200'); ?>
		<?php echo $noserub->widgetLastActiveUsers(); ?>
	</div>
</div>
