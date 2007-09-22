<?php
    $noserub_url = '/' . $session->read('Identity.local_username') . '/';
    
    if($menu['logged_in']) {
        $main_menu = array(
            'home'     => array('Home',     '/pages/home/'),
            'profile'  => array('NoseRub',  $noserub_url),
            'network'  => array('Network',  $noserub_url . 'network/'),
            'accounts' => array('Accounts', $noserub_url . 'accounts/'),
            'contacts' => array('Contacts', $noserub_url . 'contacts/'),
            'settings' => array('Settings', $noserub_url . 'settings/')
        );  
    } else {
        $main_menu = array(
            'home' => array('NoseRub', '/')
        );
        if(NOSERUB_REGISTRATION_TYPE == 'all') {
            $main_menu['register'] = array('Register', '/pages/register/');
        }
    }
?>
<div id="mainnav">
    <ul>
        <?php foreach($main_menu as $token => $item) { ?>
            <li<?php echo $menu['main'] == $token ? ' class="active"' : ''; ?>><?php echo $html->link($item[0], $item[1]); ?></li>
        <?php } ?>
    </ul>
</div>