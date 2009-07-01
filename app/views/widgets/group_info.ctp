<div class="widget widget-group-info">
	<?php if(!empty($group_info)): ?>
		<?php
			$admins = array();
			foreach($group_info['GroupMaintainer'] as $admin) {
				$username = Identity::splitUsername($admin['username'], false);
				$admins[] = $html->link($username['local_username'], 'http://' . $admin['username']);
			}
			if($admins) {
				$admins = join(', ', $admins);
			} else {
				$admins = __('No one', true);
			}
		?>
		<div class="left">
			<!-- PUT IMAGE HERE: <img src="asdf" alt="group name image" height="140" width="140" />-->
    		<?php echo $html->link(__('New Topic', true), '/groups/new_topic/' . $group_info['Group']['slug']); ?>
		</div>
		<div class="right">
			<h2><?= $group_info['Group']['name'] ?></h2>
			<p><?= $group_info['Group']['description'] ?></p>
    		<!--<p><?php echo $noserub->link('/groups/manage_subscription/'); ?><p>-->
		</div>
	<?php endif; ?>
</div>
