<ul>
    <li>
        <?php echo $html->link(__('Settings', true), '/' . $context['logged_in_user']['local_username'] . '/settings/'); ?>
    </li>
    <li>
        <?php echo $html->link(__('Logout', true), '/pages/logout/' . $security_token); ?>
    </li>
    <li>
        <?php echo $this->element('languages'); ?>
    </li>
</ul>