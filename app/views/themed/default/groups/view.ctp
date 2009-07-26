<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
	    <?php echo $noserub->widgetGroupHead(); ?>
	</div>
	<div id="bd-main-bd">
    	<?php echo $noserub->widgetFlashMessage(); ?>
	    <?php echo $noserub->widgetGroupInfo(); ?>
	    <?php echo $noserub->widgetPhotos(); ?>
		<?php echo $noserub->widgetGroupOverview(); ?>
	</div>

	<div id="bd-main-sidebar">
		<div class="widget widget-join-group">
		  <?php echo $noserub->link('/groups/manage_subscription/'); ?>
		</div>
		<?php echo $noserub->widgetGroupMembers(); ?>
		<?php echo $noserub->widgetGroupStatistics(); ?>
		<?php echo $noserub->widgetPopularGroups(); ?>
		<?php echo $noserub->widgetNewGroups(); ?>
		<?php echo $noserub->widgetGroups(); ?>
	</div>
</div>
