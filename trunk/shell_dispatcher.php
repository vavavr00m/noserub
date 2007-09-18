<?php
/* SVN FILE: $Id $ */
/**
* This is a copy of cakes index.php customized to use in cli mode especially for cron jobs.
* 
* To get cake running in cli mode we added some vars/environment to emulate web usage.
*
* All defines should only be edited if you have cake installed in
* a directory layout other than the way it is distributed.
* Each define has a commented line of code that explains what you would change.
*
*/

/**
 *  Get Cake's root directory
 */
	define('APP_DIR', 'app');
	define('DS', DIRECTORY_SEPARATOR);
	define('ROOT', dirname(__FILE__));
	define('WEBROOT_DIR', 'webroot');
	define('WWW_ROOT', ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS);
	
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('ROOT')) {
    define('ROOT', dirname(dirname(dirname(__FILE__))));
}

if (!defined('APP_DIR')) {
    define('APP_DIR', basename(dirname(dirname(__FILE__))));
}

if (!defined('CAKE_CORE_INCLUDE_PATH')) {
    define('CAKE_CORE_INCLUDE_PATH', ROOT);
}

if (!defined('WEBROOT_DIR')) {
    define('WEBROOT_DIR', basename(dirname(__FILE__)));
}

if (!defined('WWW_ROOT')) {
    define('WWW_ROOT', dirname(__FILE__) . DS);
}
if (!defined('CORE_PATH')) {
    if (function_exists('ini_set')) {
        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . CAKE_CORE_INCLUDE_PATH . PATH_SEPARATOR . ROOT . DS . APP_DIR . DS);
        define('APP_PATH', null);
        define('CORE_PATH', null);
    } else {
        define('APP_PATH', ROOT . DS . APP_DIR . DS);
        define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
    }
}

// Dispatch the controller action given to it
// e.g.: $> php shell_dispatcher.php /sync/all
$usage = 'usage: php ' . $argv[0] . ' <route>' . "\n";
if($argc == 2) {
    /**
    * Tell the application that it's running in cli mode.
    */
    define('SHELL_DISPATCHER', 1);
    if (!defined('DISABLE_DEFAULT_ERROR_HANDLING')) {
        /**
        * Disable DebuggerComponent in cli mode.
        */
        define('DISABLE_DEFAULT_ERROR_HANDLING', 1);
    }
    
    if(in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
        echo $usage;
        exit;
    }
    
    $_SERVER['REQUEST_URI'] = $argv[1];       
    /**
    * @uses cake/bootstrap.php
    */
    require CORE_PATH . 'cake' . DS . 'bootstrap.php';

    if(defined('NOSERUB_FULL_BASE_URL') && !defined('FULL_BASE_URL')) {
        define('FULL_BASE_URL', NOSERUB_FULL_BASE_URL);
    }

    define('SHELL_START_TIMESTAMP', date('Y-m-d H:i:s'));
    $Dispatcher= new Dispatcher();
    $Dispatcher->dispatch($argv[1]);
} else {
    echo $argv[0] . ': error: wrong parameter count (' . ($argc - 1) . ")\n";
    echo $usage;
}
?>