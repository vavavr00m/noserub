<?php if($context['logged_in_identity']) { ?>
    <?php echo sprintf(__('Hi %s!', true), $context['logged_in_identity']['name']); ?>
    <ul id="nav main">
        <li id="home">
            <?php echo $html->link(__('Home', true), '/' . $context['logged_in_identity']['local_username'] . '/network/'); ?>
        </li>
        <li id="contacts">
            <h2><?php __('Contacts'); ?></h2><span><?php __('close'); ?></span>
            <ul>
                <li><?php echo $html->link(__('My Contacts', true), '/' . $context['logged_in_identity']['local_username'] . '/contacts/'); ?></li>
            </ul>
        </li>
        <li id="profile">
            <?php echo $html->link(__('My Profile', true), '/' . $context['logged_in_identity']['local_username'] . '/'); ?>
        </li>
    </ul>
    <?php echo $noserub->widgetAdminMenu(); ?>
<?php } else { ?>
    <?php echo $this->element('login'); ?>
<?php } ?>
