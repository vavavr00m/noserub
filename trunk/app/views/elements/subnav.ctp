<?php
$uri = $_SERVER['REQUEST_URI'];
$username = isset($about_identity['local_username']) ? $about_identity['local_username'] : $session->read('Identity.local_username');
$noserub_url = '/' . $username . '/';
if(isset($filter)) {
    $class_all   = ($filter == 'all' || $filter == false) ? ' class="active"' : '';
    $class_media = $filter == 'media' ? ' class="active"' : '';
    $class_link  = $filter == 'link' ? ' class="active"' : '';
    $class_text  = $filter == 'text' ? ' class="active"' : '';
    $class_micro = $filter == 'micropublish' ? ' class="active"' : '';
    $class_event = $filter == 'event' ? ' class="active"' : '';
}

if($uri == '/' ||
   strpos($uri, '/jobs/') !== false ||
   strpos($uri, '/pages') !== false ||
   strpos($uri, '/accounts') > 0 ||
   strpos($uri, '/contacts') > 0 ||
   strpos($uri, '/register') > 0 ||
   strpos($uri, '/login') > 0) {
       # no sub navigation
} else if(strpos($uri, '/network/') > 0) { ?>
    <div id="subnavigation" class="subnav wrapper">
        <ul>
            <li<?php echo $class_all;?>><?php echo $html->link('All', $noserub_url . 'network/all/'); ?></li>
            <li<?php echo $class_media;?>><?php echo $html->link('Media', $noserub_url . 'network/media/'); ?></li>
            <li<?php echo $class_link;?>><?php echo $html->link('Links', $noserub_url . 'network/link/'); ?></li>
            <li<?php echo $class_text;?>><?php echo $html->link('Text', $noserub_url . 'network/text/'); ?></li>
            <li<?php echo $class_micro;?>><?php echo $html->link('Micropublish', $noserub_url . 'network/micropublish/'); ?></li>
            <li<?php echo $class_event;?>><?php echo $html->link('Events', $noserub_url . 'network/event/'); ?></li>
        </ul>
    </div>
<?php } else if(strpos($uri, '/settings/') > 0) {
    $class_profile = '';
    $class_password = strpos($uri, '/password/') > 0 ? ' class="active"' : '';
    $class_privacy  = strpos($uri, '/privacy/') > 0 ? ' class="active"' : '';
    if($class_password == '' && $class_privacy == '') {
        $class_profile = ' class="active"';
    } ?>
    <div id="subnavigation" class="subnav wrapper">
        <ul>
            <li<?php echo $class_profile; ?>><?php echo $html->link('Profile', $noserub_url . 'settings/profile/'); ?></li>  
            <li<?php echo $class_privacy; ?>><?php echo $html->link('Privacy', $noserub_url . 'settings/privacy/'); ?></li>  
            <li<?php echo $class_password; ?>><?php echo $html->link('Password', $noserub_url . 'settings/password/'); ?></li>
        </ul>
    </div>
<?php } else { ?>
    <div id="subnavigation" class="subnav wrapper">
        <ul>
            <li<?php echo $class_all;?>><?php echo $html->link('All', $noserub_url . 'all/'); ?></li>  
            <li<?php echo $class_media;?>><?php echo $html->link('Media', $noserub_url . 'media/'); ?></li>
            <li<?php echo $class_link;?>><?php echo $html->link('Links', $noserub_url . 'link/'); ?></li>  
            <li<?php echo $class_text;?>><?php echo $html->link('Text', $noserub_url . 'text/'); ?></li>  
            <li<?php echo $class_micro;?>><?php echo $html->link('Micropublish', $noserub_url . 'micropublish/'); ?></li>
            <li<?php echo $class_event;?>><?php echo $html->link('Events', $noserub_url . 'event/'); ?></li>  
        </ul>
    </div>
<?php } ?>