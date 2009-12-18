<div class="widget widget-group-info">
	<?php if(!empty($group_info)): ?>
		<div class="left">
			<!-- PUT IMAGE HERE: <img src="asdf" alt="group name image" height="140" width="140" />-->
			<?php echo $html->link('<span></span>'.__('New Topic', true), '/groups/new_topic/'.$group_info['Group']['slug'], array('escape' => false, 'class' => 'button')); ?>
		</div>
		<div class="right">
			<h2><?php echo $group_info['Group']['name']; ?></h2>
			<p><?php echo $group_info['Group']['description']; ?></p>
		</div>
	<?php endif; ?>
</div>
