<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<h1><?php echo sprintf(__('Welcome to %s', true), Context::networkName()); ?></h1>
	</div>
	<div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
		<?php echo $noserub->widgetRecentPhotos(); ?>
		<?php echo $noserub->widgetRecentVideos(); ?>
		<?php echo $noserub->widgetRecentMicropublish(); ?>
		<?php echo $noserub->widgetRecentLinks(); ?>
		<?php echo $noserub->widgetRecentTexts(); ?>
		<?php echo $noserub->widgetRecentAudio(); ?>
		<?php echo $noserub->widgetRecentLocations(); ?>
		<?php echo $noserub->widgetRecentEvents(); ?>
		<?php echo $noserub->widgetRecentDocuments(); ?>
		<?php echo $noserub->widgetRecentNoserubEvents(); ?>
	</div>
	<div id="bd-main-sidebar">
        <?php echo $noserub->widgetAd('sidebar'); ?>
        <?php echo $noserub->widgetPopularGroups(); ?>
		<?php echo $noserub->widgetLastActiveUsers(); ?>
		<?php echo $noserub->widgetNewUsers(); ?>
	</div>
</div>
