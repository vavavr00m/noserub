<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<h2><?php __('Admin'); ?></h2>
	</div>
	<div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
		<?php echo $noserub->formAdminSettings(); ?>
	</div>

	<div id="bd-main-sidebar">
		<?php echo $noserub->widgetAdminLogin(); ?>
		<?php echo $noserub->widgetAdminNavigation(); ?>
	</div>
</div>
