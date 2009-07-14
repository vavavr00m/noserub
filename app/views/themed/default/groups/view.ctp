<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
	    <?php echo $noserub->widgetGroupHead(); ?>
	</div>
	<div id="bd-main-bd">
    	<?php echo $noserub->widgetFlashMessage(); ?>
	    <?php echo $noserub->widgetGroupInfo(); ?>
		<?php echo $noserub->widgetGroupOverview(); ?>
	</div>

	<div id="bd-main-sidebar">
		<div class="widget widget-join-group">
		  <?php echo $noserub->link('/groups/manage_subscription/'); ?>
		</div>
		<?php echo $noserub->widgetGroupMembers(); ?>
		<div class="widget widget-group-statistics">
			<h2>Statistics</h2>
			<ul>
				<li><strong>Created</strong> 23 Jan 2009</li>
				<li><strong>Members</strong> 599</li>
				<li><strong>Posts</strong> 12</li>
				<li><strong>Administrator</strong> <a href="#">Alexander Kaiser</a></li>
			</ul>
		</div>

		<?php echo $noserub->widgetPopularGroups(); ?>
		<?php echo $noserub->widgetNewGroups(); ?>
		<?php echo $noserub->widgetGroups(); ?>
	</div>
</div>
