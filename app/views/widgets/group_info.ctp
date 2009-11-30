<div class="widget widget-group-info">
	<?php if(!empty($group_info)): ?>
		<div class="left">
			<!-- PUT IMAGE HERE: <img src="asdf" alt="group name image" height="140" width="140" />-->
			<a class="button" href="/groups/new_topic/<?php echo $group_info['Group']['slug']; ?>">
				<span></span>
				<?php __('New Topic') ?>
			</a>
		</div>
		<div class="right">
			<h2><?php echo $group_info['Group']['name']; ?></h2>
			<p><?php echo $group_info['Group']['description']; ?></p>
		</div>
	<?php endif; ?>
</div>
