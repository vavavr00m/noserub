<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<?php echo $noserub->widgetProfile(); ?>
	</div>
	<div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
		<?php echo $noserub->widgetPhotos(); ?>
		<?php echo $noserub->widgetSingleLifestream(); ?>
	</div>

	<div id="bd-main-sidebar">
		<?php echo $noserub->widgetCommunications(); ?>
		<?php echo $noserub->widgetContacts(); ?>
		<?php echo $noserub->widgetAccounts(); ?>
		<?php echo $noserub->widgetGroups(); ?>
		<?php echo $noserub->widgetNetworks(); ?>
	</div>
</div>
