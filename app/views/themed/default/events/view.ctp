<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
	    <?php echo $noserub->widgetEventHead(); ?>
	</div>
	<div id="bd-main-bd">
    	<?php echo $noserub->widgetFlashMessage(); ?>
    	<?php echo $noserub->widgetPhotos(); ?>
	    <?php echo $noserub->widgetEventOverview(); ?>
	    <?php echo $noserub->formAddEntry(); ?>
	</div>

	<div id="bd-main-sidebar">
	    <?php echo $noserub->widgetEventInfo(); ?>
	    <?php echo $noserub->widgetMap(); ?>
	</div>
</div>
