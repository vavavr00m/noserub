<?php if(Configure::read('context.admin_id')) { ?>
    You're logged in as admin_id <?php echo Configure::read('context.admin_id'); ?>.
    <ul>
        <li><?php echo $html->link(__('Settings', true), '/admins/'); ?></li>
        <li><?php echo $html->link(__('Change password', true), '/admins/password/'); ?></li>
        <li><?php echo $html->link(__('Logout', true), '/admins/logout/_t:' . $security_token); ?></li>
    </ul>
<?php } ?>