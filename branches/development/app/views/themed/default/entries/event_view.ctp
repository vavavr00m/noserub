<div id="bd-main" class="with-sidebar">
    <div id="bd-main-bd">
    	<div id="bd-main-hd">
    		<?php echo $noserub->widgetEventHead(); ?>
    	</div>
    	<div id="bd-main-bd">
        	<?php echo $noserub->widgetFlashMessage(); ?>
    		<?php echo $noserub->widgetViewEntry(); ?>
    		<?php echo $noserub->formAddComment(); ?>
    	</div>
    </div>
    <div id="bd-main-sidebar">
        <?php echo $noserub->widgetEventInfo(); ?>
        <?php echo $noserub->widgetAd('sidebar'); ?>
	    <?php echo $noserub->widgetMap(); ?>
	</div>
</div>