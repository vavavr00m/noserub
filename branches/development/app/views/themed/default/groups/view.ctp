<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
	    <?php echo $noserub->widgetGroupHead(); ?>
	</div>
	<div id="bd-main-bd">
    	<?php echo $noserub->widgetFlashMessage(); ?>
	    <?php echo $noserub->widgetGroupInfo(); ?>
	    <?php echo $noserub->widgetGroupOverview(); ?>
	    <?php echo $noserub->widgetRecentPhotos(); ?>
	</div>

	<div id="bd-main-sidebar">
		<div class="widget widget-join-group">
		  <?php echo $noserub->link('/groups/manage_subscription/'); ?>
		</div>
		<?php echo $noserub->widgetGroupStatistics(); ?>
		<?php echo $noserub->widgetAd('sidebar'); ?>
		<?php echo $noserub->widgetGroupMembers(); ?>
	</div>
</div>
