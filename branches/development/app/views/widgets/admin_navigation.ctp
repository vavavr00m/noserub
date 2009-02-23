<?php if($context['admin_id']) { ?>
    You're logged in as admin_id <?php echo $context['admin_id']; ?>.
    <ul>
        <li><?php echo $html->link(__('Logout', true), '/admins/logout/_t:' . $security_token); ?></li>
    </ul>
<?php } ?>