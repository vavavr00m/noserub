<div class="widget widget-group-statistics">
    <?php
		$admins = array();
		foreach($group_statistics['GroupMaintainer'] as $admin) {
			$username = Identity::splitUsername($admin['username'], false);
			$admins[] = $html->link($username['local_username'], 'http://' . $admin['username']);
		}
		if($admins) {
			$admins = join(', ', $admins);
		} else {
			$admins = __('No one', true);
		}
	?>
    <h2><?php __('Statistics'); ?></h2>
    <ul>
    	<li><strong><?php __('Created'); ?></strong> <?php echo date('Y-m-d', strtotime($group_statistics['Group']['created'])); ?></li>
    	<li><strong>Members</strong> 599</li>
    	<li><strong>Posts</strong> <?php echo $group_statistics['Group']['entry_count']; ?></li>
    	<li><strong>Administrator</strong> <?php echo $admins; ?></li>
    </ul>
</div>