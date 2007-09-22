<?php
$username = isset($about_identity['local_username']) ? $about_identity['local_username'] : $session->read('Identity.local_username');

$sub_menu = null;

if($menu['main'] == 'network' || $menu['main'] == 'profile') {
    $noserub_url = '/' . $username . '/';
    if($menu['main'] == 'network') {
        $noserub_url .= 'network/';
    } 
    $sub_menu = array(
        'all'          => array('All', $noserub_url . 'all/'),
        'photo'        => array('Photo', $noserub_url . 'photo/'),
        'video'        => array('Video', $noserub_url . 'video/'),
        'audio'        => array('Audio', $noserub_url . 'audio/'),
        'link'         => array('Link',  $noserub_url . 'link/'),
        'text'         => array('Text', $noserub_url . 'text/'),
        'micropublish' => array('Micropublish', $noserub_url . 'micropublish/'),
        'event'        => array('Events', $noserub_url . 'event/')
    );    
} else if($menu['main'] == 'settings') {
    $noserub_url = '/' . $username . '/settings/';
    $sub_menu = array(
        'profile'  => array('Profile', $noserub_url . 'profile/'),
        'privacy'  => array('Privacy', $noserub_url . 'privacy/'),
        'password' => array('Password', $noserub_url . 'password/') 
    );
}

if($sub_menu) { ?>
    <div id="subnavigation" class="subnav wrapper">
        <ul>
            <?php foreach($sub_menu as $token => $item) { ?>
                <li<?php echo $menu['sub'] == $token ? ' class="active"' : ''; ?>><?php echo $html->link($item[0], $item[1]); ?></li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>