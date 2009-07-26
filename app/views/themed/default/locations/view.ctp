<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
	    <?php echo $noserub->widgetLocationHead(); ?>
	</div>
	<div id="bd-main-bd">
    	<?php echo $noserub->widgetFlashMessage(); ?>
    	<?php echo $noserub->widgetPhotos(); ?>
	    <?php echo $noserub->widgetLocationOverview(); ?>
	    <?php echo $noserub->formAddEntry(); ?>
	</div>

	<div id="bd-main-sidebar">
	    <?php echo $noserub->widgetLocationInfo(); ?>
	    <?php echo $noserub->widgetMap(); ?>
	</div>
</div>
