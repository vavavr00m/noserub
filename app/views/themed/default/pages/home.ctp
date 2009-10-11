<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<h1><?php echo sprintf(__('Welcome to %s', true), Context::networkName()); ?></h1>
	</div>
	<div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
	</div>
	<div id="bd-main-sidebar">
        <?php echo $noserub->widgetAd('sidebar'); ?>
		<?php echo $noserub->widgetLastActiveUsers(); ?>
	</div>
</div>
