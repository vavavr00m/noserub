<?php echo sprintf(__('Hi %s!', true), $context['logged_in_user']['name']); ?>
<ul>
    <li>
        <?php echo $html->link(__('Home', true), '/' . $context['logged_in_user']['local_username'] . '/network/'); ?>
    </li>
    <li>
        <?php echo $html->link(__('My Contacts', true), '/' . $context['logged_in_user']['local_username'] . '/contacts/'); ?>
    </li>
    <li>
        <?php echo $html->link(__('My Profile', true), '/' . $context['logged_in_user']['local_username'] . '/'); ?>
    </li>
</ul>