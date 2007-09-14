<?php

$uri = $_SERVER['REQUEST_URI'];
$username = isset($url_username) ? $url_username : $session->read('Identity.local_username');
$noserub_url = '/' . $username;
if(strpos($uri, '/accounts/') > 0 ||
   strpos($uri, '/contacts/') > 0 ||
   strpos($uri, '/register/') > 0 ||
   strpos($uri, '/login/') > 0) {
       # no sub navigation
} else if(strpos($uri, '/network/') > 0) { ?>
    <div id="subnavigation" class="subnav wrapper">
        <ul>
            <li class="first"><?php echo $html->link('All', $noserub_url . '/network/all/'); ?></li>
            <li class="first"><?php echo $html->link('Media', $noserub_url . '/network/media/'); ?></li>
            <li class="first"><?php echo $html->link('Links', $noserub_url . '/network/link/'); ?></li>
            <li class="first"><?php echo $html->link('Text', $noserub_url . '/network/text/'); ?></li>
            <li class="first"><?php echo $html->link('Event', $noserub_url . '/network/event/'); ?></li>
        </ul>
    </div>
<?php } else if(strpos($uri, '/noserub/') === 0) { ?>
    <div id="subnavigation" class="subnav wrapper">
        <ul>
            <li><?php echo $html->link('All', $noserub_url . '/all/'); ?></li>  
            <li><?php echo $html->link('Media', $noserub_url . '/media/'); ?></li>
            <li><?php echo $html->link('Links', $noserub_url . '/link/'); ?></li>  
            <li><?php echo $html->link('Text', $noserub_url . '/text/'); ?></li>  
            <li><?php echo $html->link('Event', $noserub_url . '/event/'); ?></li>  
        </ul>
    </div>
<?php } else if(strpos($uri, '/settings/') > 0) { ?>
    <div id="subnavigation" class="subnav wrapper">
        <ul>
            <li><?php echo $html->link('Profile', $noserub_url . '/settings/profile/'); ?></li>  
            <li><?php echo $html->link('Privacy', $noserub_url . '/settings/privacy/'); ?></li>  
            <li><?php echo $html->link('Password', $noserub_url . '/settings/password/'); ?></li>
        </ul>
    </div>
<?php } ?>