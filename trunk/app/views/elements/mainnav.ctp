<?php
    $noserub_url = '/' . $session->read('Identity.local_username') . '/';
    
    if($menu['logged_in']) {
        $main_menu = array(
            'dashboard' => array('Dashboard', '/pages/dashboard/'),
            'profile'   => array('My Profile',   $noserub_url),
            'network'   => array('Network',   $noserub_url . 'network/'),
            'contacts'  => array('Contacts',  $noserub_url . 'contacts/'),
            'accounts'  => array('Your Social Activity',  $noserub_url . 'accounts/'),
            'settings'  => array('Settings',  $noserub_url . 'settings/')
        );  
    } else {
        $main_menu = array(
            'home' => array('Home', '/')
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