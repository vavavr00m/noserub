<div id="bd-main" class="with-sidebar">
	 <div id="bd-main-bd">
		<?php echo $noserub->widgetFlashMessage(); ?>
		<?php echo $noserub->widgetContacts(array('layout' => 'list')); ?>
	</div>

	<div id="bd-main-sidebar">
		<?php echo $noserub->widgetContactFilter(); ?>
	</div>
</div>
