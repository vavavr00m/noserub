<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<h1><?php echo sprintf(__('Welcome to %s', true), Context::networkName()); ?></h1>
	</div>
	<div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
		<?php echo $noserub->widgetPhotos(); ?>
		<?php echo $noserub->widgetVideos(); ?>
		<?php echo $noserub->widgetNetworkLifestream(); ?>
	</div>
	<div id="bd-main-sidebar">
	    <?php echo $noserub->widgetEntryFilter(); ?>
        <?php echo $noserub->widgetAd('sidebar'); ?>
		<?php echo $noserub->widgetLastActiveUsers(); ?>
	</div>
</div>
