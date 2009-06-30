<?php if(!empty($group_info)) { ?>
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
    <h2><?php __('Group Info'); ?></h2>
    <ul>
        <li>Name: <?php echo $group_info['Group']['name']; ?></li>
        <li>Description: <?php echo $group_info['Group']['description']; ?></li>
        <li>Admins: <?php echo $admins; ?></li>
    </ul>
    <?php echo $noserub->link('/groups/manage_subscription/'); ?>
<?php } ?>