<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<h2><?php __('Groups'); ?></h2>
	</div>
	<div id="bd-main-bd">
    	<?php echo $noserub->widgetFlashMessage(); ?>
		<?php echo $noserub->widgetGroupsOverview(); ?>
	</div>

	<div id="bd-main-sidebar">
	    <?php echo $noserub->widgetAd('sidebar'); ?>
		<?php echo $noserub->widgetGroups(); ?>
	</div>
</div>
