<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<?php echo $noserub->widgetProfile(); ?>
	</div>
	<div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
		<?php echo $noserub->widgetFavorites(); ?>
	</div>

    <div id="bd-main-sidebar">
		<?php echo $noserub->widgetCommunications(); ?>
		<?php echo $noserub->widgetAccounts(); ?>
		<?php echo $noserub->widgetGroups(); ?>
	</div>
</div>
