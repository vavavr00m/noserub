<?php

if(file_exists(APP . '/config/noserub.php')) {
    require_once 'noserub.php';
} else {
    die('noserub.php not found!');
}

/**
 * This is just a simple wrapper to make
 * the 'Context' more visible.
 */
class Context {
    public static function read($key = '') {
        if($key) {
            $key = 'context.' . $key;
        }
        return Configure::read($key);
    }
    
    public static function write($key, $value) {
        return Configure::write('context' . $key, $value);
    }    
}

/**
 * a "context" array that will hold information about
 * the current status. That means: which pages is being
 * displayed, which is the logged in user, etc..
 * The goal is to have this universally available in all
 * controllers and all views.
 */
Context::write('', array(
    'logged_in_identity' => false,
    'network' => array('id' => 1), # default for now, needed for old menu component
    'identity' => false, # the identity we're looking at,
    'is_self' => false, # wether the identity we look at is the logged in identity
    'is_guest' => false, # wether the identity only logged in with OpenID, without account
    'admin_id' => false # wether the identity is logged in with admin access right now
));

Configure::write('NoseRub.version', '0.9a');

define('NOSERUB_VALID_USERNAME', '/^[\w.-_]+$/ism');

# to exclude pages, tests and jobs is essential here, because else, 
# the routes would not be working. excluding the others is
# just a precaution for avoiding confusions.
define('NOSERUB_RESERVED_USERNAMES', 'api,pages,jobs,tests,noserub,auth,login,register,social_stream,search,groups,entry,networks,contacts');

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

# this is no real data for bootstrap.php, but I figure, "functions.php" isn't
# suitable in /app/config/ either. So, as long as I don't know what to do with
# it, I will leave it here.
function sort_items($a, $b) {
	return $a['Entry']['published_on'] < $b['Entry']['published_on'];
}

function sort_accounts($a, $b) {
    $a_title = isset($a['Account']) ? $a['Account']['title'] : $a['title'];
    $b_title = isset($b['Account']) ? $b['Account']['title'] : $b['title'];
    
    $a_val = $a['Service']['id'] == 8 ? $a_title : $a['Service']['name'];
    $b_val = $b['Service']['id'] == 8 ? $b_title : $b['Service']['name'];
    return strtolower($a_val) > strtolower($b_val);
}