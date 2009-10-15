<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<?php echo $noserub->widgetProfile(); ?>
        <?php echo $noserub->widgetFoaf(); ?>
	</div>
	<div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
		<?php echo $noserub->widgetPhotos(); ?>
		<?php echo $noserub->widgetVideos(); ?>
		<?php echo $noserub->widgetSingleLifestream(); ?>
	</div>

	<div id="bd-main-sidebar">
	    <?php echo $noserub->widgetMap(); ?>
	    <?php echo $noserub->widgetEntryFilter(); ?>
		<?php echo $noserub->widgetCommunications(); ?>
		<?php echo $noserub->widgetContacts(); ?>
		<?php echo $noserub->widgetAccounts(); ?>
		<?php echo $noserub->widgetGroups(); ?>
	</div>
</div>
