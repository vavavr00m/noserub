<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<h1><?php echo __('Group') ?> <span><?php echo $group['Group']['name']; ?></span></h2>
		<ul>
			<li><?php echo $html->link(__('All Groups', true), '#'); ?></li>
			<li><?php echo $html->link(__('My Groups', true), '#'); ?></li>
			<li><?php echo $html->link(__('Search A Group', true), '#'); ?></li>
		</ul>
	</div>
	<div id="bd-main-bd">
    	<?php echo $noserub->widgetFlashMessage(); ?>
	    <?php echo $noserub->widgetGroupInfo(); ?>
		<?php echo $noserub->widgetGroupOverview(); ?>
	</div>

	<div id="bd-main-sidebar">
		<?php echo $noserub->widgetPopularGroups(); ?>
		<?php echo $noserub->widgetNewGroups(); ?>
		<?php echo $noserub->widgetGroups(); ?>
	</div>
</div>
