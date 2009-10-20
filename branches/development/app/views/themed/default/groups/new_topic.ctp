<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<?php echo $noserub->widgetGroupHead(); ?>
	</div>
	<div id="bd-main-bd">
	    <h2><?php __('New Topic'); ?></h2>
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