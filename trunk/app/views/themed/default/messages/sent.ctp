<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<h2><?php __('Messages'); ?></h2>
	</div>
	<div id="bd-main-bd">
    	<?php echo $noserub->widgetFlashMessage(); ?>
    	<?php echo $noserub->widgetMessages('sent'); ?>
	</div>

	<div id="bd-main-sidebar">
        <?php echo $noserub->messagesNavigation(); ?>
	</div>
</div>
