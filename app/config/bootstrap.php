<?php

if(file_exists(APP . '/config/noserub.php')) {
    require_once 'noserub.php';
} else {
    die('noserub.php not found!');
}

require_once('context.php');

Configure::write('NoseRub.version', '0.9a');

define('NOSERUB_VALID_USERNAME', '/^[\w.-_]+$/ism');

# to exclude pages, tests and jobs is essential here, because else, 
# the routes would not be working. excluding the others is
# just a precaution for avoiding confusions.
define('NOSERUB_RESERVED_USERNAMES', 'auth,api,comments,contacts,entry,groups,jobs,login,networks,noserub,oauth,pages,register,search,settings,social_stream,tests');

# in a cli environment FULL_BASE_URL is not defined, so we have to do it manually
if(!defined('FULL_BASE_URL')) {
	define('FULL_BASE_URL', substr(Context::read('network.url'), 0, -1));
}

Configure::write('Languages', array(
    'de-de' => 'Deutsch',
    'en-en' => 'English',
    'fr-fr' => 'Français',
    #'es-es' => 'Español',
    'ko-kr' => '한국어', # Korean
    'nn'    => 'Norsk',
    'fi'    => 'Suomi',
    'sv'    => 'Svenska',
    'tr'    => 'Turkish'
));

/**
 * Static files directory
 * @name STATIC_DIR
 */ 
$static_dir = APP . WEBROOT_DIR . DS . 'static' . DS;
define('STATIC_DIR', $static_dir);
define('AVATAR_DIR', STATIC_DIR . 'avatars' . DS);
define('PHOTO_DIR', STATIC_DIR . 'photos' . DS);

# this is no real data for bootstrap.php, but I figure, "functions.php" isn't
# suitable in /app/config/ either. So, as long as I don't know what to do with
# it, I will leave it here.
function sort_items($a, $b) {
	return $a['Entry']['published_on'] < $b['Entry']['published_on'];
}

$service_types = array(
    0 => array(
        'token' => 'noserub',
        'name' => __('NoseRub', true),
        'intro' => '@user@ @item@'
    ),
    1 => array(
        'token' => 'photo',
        'name' => __('Photos', true),
        'intro' => __('@user@ took a photo called @item@', true) 
    ),
    2 => array(
        'token' => 'link',
        'name' => __('Links / Bookmarks', true),
        'intro' => __('@user@ bookmarked @item@', true)
    ),
    3 => array(
        'token' => 'text',
        'name' => __('Text / Blog', true),
        'intro' => __('@user@ wrote a text about @item@', true)
    ),
    4 => array(
        'token' => 'event',
        'name' => __('Event', true),
        'intro' => __('@user@ plans to attend @item@', true)
    ),
    5 => array(
        'token' => 'micropublish',
        'name' => __('Micropublishing', true),
        'intro' => __('@user@ says @item@', true)
    ),
    6 => array(
        'token' => 'video',
        'name' => __('Videos', true),
        'intro' => __('@user@ made a video called @item@', true)
    ),
    7 => array(
        'token' => 'audio',
        'name' => __('Audio', true),
        'intro' => __('@user@ listens to @item@', true)
    ),
    8 => array(
        'token' => 'document',
        'name' => __('Documents', true),
        'intro' => __('@user@ uploaded a document called @item@', true)
    ),
    9 => array(
        'token' => 'location',
        'name' => __('Locations', true),
        'intro' => __('@user@ is currently at @item@', true)
    )
);

$service_types_list = array();
foreach($service_types as $id => $service_type) {
    $service_types_list[$id] = $service_type['name'];
}

Configure::write('service_types', $service_types);
Configure::write('service_types_list', $service_types_list);
    