<?php
    $uri = $_SERVER['REQUEST_URI'];
    $noserub_url = NOSERUB_URL_PREFIX . '/' . $session->read('Identity.username');
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
        <li<?php echo $class_home;?>><a href="<?php echo NOSERUB_URL_PREFIX; ?>/">NoseRub</a></li>
        <?php if($session->check('Identity.id')) { ?>
            <li<?php echo $class_noserub;?>><a href="<?php echo $noserub_url; ?>/">My NoseRub</a></li>
            <li<?php echo $class_network;?>><a href="<?php echo $noserub_url; ?>/network/">Network</a></li>
            <li<?php echo $class_accounts;?>><a href="<?php echo $noserub_url; ?>/accounts/">Accounts</a></li>
            <li<?php echo $class_contacts;?>><a href="<?php echo $noserub_url; ?>/contacts/">Contacts</a></li>
            <li<?php echo $class_settings;?>><a href="<?php echo $noserub_url; ?>/settings/">Settings</a></li>
        <?php } else {
            if(NOSERUB_REGISTRATION_TYPE == 'all') { ?>
                <li<?php echo $class_register;?>><a href="<?php echo NOSERUB_URL_PREFIX; ?>/register/">Register</a></li>
            <?php }
        } ?>
    </ul>
</div>