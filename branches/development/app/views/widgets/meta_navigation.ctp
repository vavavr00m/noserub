<ul>
    <?php if($context['logged_in_identity']) { ?>
        <li>
            <?php echo $html->link(__('Settings', true), '/' . $context['logged_in_identity']['local_username'] . '/settings/'); ?>
        </li>
        <li>
            <?php echo $html->link(__('Logout', true), '/pages/logout/' . $security_token); ?>
        </li>
    <?php } ?>
    <li>
        <?php echo $this->element('languages'); ?>
    </li>
</ul>
