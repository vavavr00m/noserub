<div id="bd-main-bd" class="with-sidebar">
	<div id="bd-main-hd">
		<h2><?php echo __('Group') . ': ' . $group['Group']['name']; ?></h2>
	</div>
	<div id="bd-main-bd">
    	<?php echo $noserub->widgetFlashMessage(); ?>
    	<?php echo $noserub->formAddEntry(); ?>
		<?php echo $noserub->widgetGroupOverview(); ?>
	</div>

	<div id="bd-main-sidebar">
		<?php echo $noserub->widgetPopularGroups(); ?>
		<?php echo $noserub->widgetNewGroups(); ?>
		<?php echo $noserub->widgetGroups(); ?>
	</div>
</div>
