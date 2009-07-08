<div id="bd-main" class="with-sidebar">
	<div id="bd-main-hd">
		<h1><?php __('Group') ?> <span><?php echo $group['Group']['name']; ?></span></h2>
	</div>
	<div id="bd-main-bd">
    	<?php echo $noserub->widgetFlashMessage(); ?>
	    <?php echo $noserub->widgetGroupInfo(); ?>
		<?php echo $noserub->widgetGroupOverview(); ?>
	</div>

	<div id="bd-main-sidebar">
		<div class="widget widget-join-group">
			<a class="button" href="#"><span></span><?php __('Join this group') ?></a>
		</div>
		<div class="widget widget-group-members">
			<h2>[GROUP NAME] members</h2>
			<ul>
				<li><a href="#"><img src="images/examples/userimage1_medium.jpg" alt="" /></a></li>
				<li><a href="#"><img src="images/examples/userimage2_medium.jpg" alt="" /></a></li>
				<li><a href="#"><img src="images/examples/userimage3_medium.jpg" alt="" /></a></li>
				<li><a href="#"><img src="images/examples/userimage4_medium.jpg" alt="" /></a></li>
				<li><a href="#"><img src="images/examples/userimage5_medium.jpg" alt="" /></a></li>
				<li><a href="#"><img src="images/examples/userimage6_medium.jpg" alt="" /></a></li>
			</ul>
			<p class="more">
				<a href="#">show more</a>
			</p>
		</div>
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
