<?php
    $uri = $_SERVER['REQUEST_URI'];
    $noserub_url = '/' . $session->read('Identity.username');
    $class_accounts = '';
    $class_network  = '';
    $class_contacts = '';
    $class_settings = '';
    $class_noserub  = '';
    $class_register = (strpos($uri, '/register/') === 0) ? ' class="active"' : '';
    $class_home = ($uri == '/') ? ' class="active"' : '';
    if(strpos($uri, '/accounts/') > 0) {
        $class_accounts = ' class="active"';
    } else if(strpos($uri, '/network/') > 0) {
        $class_network  = ' class="active"';
    }else if(strpos($uri, '/contacts/') > 0) {
        $class_contacts = ' class="active"';
    } else if(strpos($uri, '/settings/') > 0) {
        $class_settings = ' class="active"';
    } else if(strpos($uri, '/noserub/') === 0) {
        $class_noserub  = ' class="active"';
    }
?>
<div id="mainnav" class="nav">
    <ul>
        <li<?php echo $class_home;?>><?php echo $html->link('Noserub', '/'); ?></li>
        <?php if($session->check('Identity.id')) { ?>
            <li<?php echo $class_noserub;?>><?php echo $html->link('My NoseRub', $noserub_url . '/'); ?></li>
            <li<?php echo $class_network;?>><?php echo $html->link('Network', $noserub_url . '/network/'); ?></li>
            <li<?php echo $class_accounts;?>><?php echo $html->link('Accounts', $noserub_url . '/accounts/'); ?></li>
            <li<?php echo $class_contacts;?>><?php echo $html->link('Contacts', $noserub_url . '/contacts/'); ?></li>
            <li<?php echo $class_settings;?>><?php echo $html->link('Settings', $noserub_url . '/settings/'); ?></li>
        <?php } else {
            if(NOSERUB_REGISTRATION_TYPE == 'all') { ?>
                <li<?php echo $class_register;?>><?php echo $html->link('Register', '/pages/register/'); ?></li>
            <?php }
        } ?>
    </ul>
</div>