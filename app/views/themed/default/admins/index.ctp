<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<h1><?php __('Admin'); ?></h1>
	</div>
	<div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
	</div>

	<div id="bd-main-sidebar">
		<?php echo $noserub->widgetAdminLogin(); ?>
		<?php echo $noserub->widgetAdminNavigation(); ?>
	</div>
</div>
