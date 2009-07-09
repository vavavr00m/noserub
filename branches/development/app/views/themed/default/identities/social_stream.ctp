<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<h2><?php __("Social Stream of your contact's activities")?></h2>
	</div>
	<div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
    	<?php echo $noserub->formAddEntry(); ?>
		<?php echo $noserub->widgetNetworkLifestream(); ?>
	</div>

	<div id="bd-main-sidebar">
	    <?php echo $noserub->widgetEntryFilter(); ?>
		<?php echo $noserub->widgetContactFilter(); ?>
		<?php echo $noserub->widgetContacts(); ?>
		<?php echo $noserub->widgetNewUsers(); ?>
	</div>
</div>
