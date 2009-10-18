<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<?php echo $noserub->widgetProfile(); ?>
        <?php echo $noserub->widgetFoaf(); ?>
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
	    <?php echo $noserub->widgetMap(); ?>
	    <?php echo $noserub->widgetGroups(); ?>
		<?php echo $noserub->widgetCommunications(); ?>
		<?php echo $noserub->widgetContacts(); ?>
		<?php echo $noserub->widgetAccounts(); ?>
	</div>
</div>
