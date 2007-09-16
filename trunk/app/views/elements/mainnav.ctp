<?php
    $uri = $_SERVER['REQUEST_URI'];
    $noserub_url = '/' . $session->read('Identity.local_username') . '/';
    $class_accounts = '';
    $class_network  = '';
    $class_contacts = '';
    $class_settings = '';
    $class_register = '';
    $class_home = '';
    if(strpos($uri, '/accounts/') > 0) {
        $class_accounts = ' class="active"';
    } else if(strpos($uri, '/network/') > 0) {
        $class_network  = ' class="active"';
    }else if(strpos($uri, '/contacts/') > 0) {
        $class_contacts = ' class="active"';
    } else if(strpos($uri, '/settings/') > 0) {
        $class_settings = ' class="active"';
    } else if(strpos($uri, '/register/') !== false) {
        $class_register = ' class="active"';
    } else if(strpos($uri, $noserub_url) == 0 || $uri == '/') {
        $class_home = ' class="active"';
    }
?>
<div id="mainnav">
    <ul>
        <?php if($session->check('Identity.id')) { ?>
            <li<?php echo $class_home;?>><?php echo $html->link('NoseRub', $noserub_url); ?></li>
            <li<?php echo $class_network;?>><?php echo $html->link('Network', $noserub_url . 'network/'); ?></li>
            <li<?php echo $class_accounts;?>><?php echo $html->link('Accounts', $noserub_url . 'accounts/'); ?></li>
            <li<?php echo $class_contacts;?>><?php echo $html->link('Contacts', $noserub_url . 'contacts/'); ?></li>
            <li<?php echo $class_settings;?>><?php echo $html->link('Settings', $noserub_url . 'settings/'); ?></li>
        <?php } else { ?>
            <li<?php echo $class_home;?>><?php echo $html->link('NoseRub', '/'); ?></li>
            <?php if(NOSERUB_REGISTRATION_TYPE == 'all') { ?>
                <li<?php echo $class_register;?>><?php echo $html->link('Register', '/pages/register/'); ?></li>
            <?php }
        } ?>
    </ul>
</div>