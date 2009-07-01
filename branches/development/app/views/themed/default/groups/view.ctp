<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<h2><?php echo __('Group') . ': ' . $group['Group']['name']; ?></h2>
	</div>
	<div id="bd-main-bd">
    	<?php echo $noserub->widgetFlashMessage(); ?>
    	<?php echo $html->link(__('New Topic', true), '/groups/new_topic/' . $group['Group']['slug']); ?>
		<?php echo $noserub->widgetGroupOverview(); ?>
	</div>

	<div id="bd-main-sidebar">
	    <?php echo $noserub->widgetGroupInfo(); ?>
		<?php echo $noserub->widgetPopularGroups(); ?>
		<?php echo $noserub->widgetNewGroups(); ?>
		<?php echo $noserub->widgetGroups(); ?>
	</div>
</div>