<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<h2><?php echo __('Group') . ': ' . $group['Group']['name']; ?></h2>
	</div>
	<div id="bd-main-bd">
	    <h2><?php __('New Topic'); ?>
    	<?php echo $noserub->widgetFlashMessage(); ?>
    	<?php echo $noserub->formAddEntry(); ?>
	</div>

	<div id="bd-main-sidebar">
	    <?php echo $noserub->widgetGroupInfo(); ?>
		<?php echo $noserub->widgetPopularGroups(); ?>
		<?php echo $noserub->widgetNewGroups(); ?>
		<?php echo $noserub->widgetGroups(); ?>
	</div>
</div>