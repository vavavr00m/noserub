<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<h2><?php __('Add a new group'); ?></h2>
	</div>
	<div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
		<?php echo $noserub->formGroupsAdd(); ?>
	</div>

	<div id="bd-main-sidebar">
		<?php echo $noserub->widgetPopularGroups(); ?>
		<?php echo $noserub->widgetNewGroups(); ?>
		<?php echo $noserub->widgetGroups(); ?>
	</div>
</div>
