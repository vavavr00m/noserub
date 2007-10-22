<?php
    $noserub_url = '/' . $session->read('Identity.local_username') . '/';
    
    if($menu['logged_in']) {
        $main_menu = array(
            'network'     => array('Social Graph', '/pages/social_graph/'),
            'my_profile'  => array('My Profile',   $noserub_url),
            'my_contacts' => array('My Contacts',   $noserub_url . '/contacts/'),
            'settings'    => array('Settings',  $noserub_url . 'settings/')
        );  
    } else {
        $main_menu = array(
            'social_graph' => array('Social Graph', '/')
        );
        if(NOSERUB_REGISTRATION_TYPE == 'all') {
            $main_menu['register'] = array('Add me!', '/pages/register/');
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