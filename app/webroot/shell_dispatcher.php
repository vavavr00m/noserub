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
 * Do not change
 */
	if (!defined('DS')) {
		 define('DS', DIRECTORY_SEPARATOR);
	}
/**
 * These defines should only be edited if you have cake installed in
 * a directory layout other than the way it is distributed.
 * Each define has a commented line of code that explains what you would change.
 */
	if (!defined('ROOT')) {
		 //define('ROOT', 'FULL PATH TO DIRECTORY WHERE APP DIRECTORY IS LOCATED. DO NOT ADD A TRAILING DIRECTORY SEPARATOR');
		 //You should also use the DS define to separate your directories
		 define('ROOT', dirname(dirname(dirname(__FILE__))));
	}
	if (!defined('APP_DIR')) {
		 //define('APP_DIR', 'DIRECTORY NAME OF APPLICATION');
		 define('APP_DIR', basename(dirname(dirname(__FILE__))));
	}
/**
 * This only needs to be changed if the cake installed libs are located
 * outside of the distributed directory structure.
 */
	if (!defined('CAKE_CORE_INCLUDE_PATH')) {
		 //define ('CAKE_CORE_INCLUDE_PATH', 'FULL PATH TO DIRECTORY WHERE CAKE CORE IS INSTALLED. DO NOT ADD A TRAILING DIRECTORY SEPARATOR');
		 //You should also use the DS define to separate your directories
		 define('CAKE_CORE_INCLUDE_PATH', ROOT);
	}
///////////////////////////////
//DO NOT EDIT BELOW THIS LINE//
///////////////////////////////
	if (!defined('WEBROOT_DIR')) {
		 define('WEBROOT_DIR', basename(dirname(__FILE__)));
	}
	if (!defined('WWW_ROOT')) {
		 define('WWW_ROOT', dirname(__FILE__) . DS);
	}
	if (!defined('CORE_PATH')) {
		 if (function_exists('ini_set')) {
			  ini_set('include_path', CAKE_CORE_INCLUDE_PATH . PATH_SEPARATOR . ROOT . DS . APP_DIR . DS . PATH_SEPARATOR . ini_get('include_path'));
			  define('APP_PATH', null);
			  define('CORE_PATH', null);
		 } else {
			  define('APP_PATH', ROOT . DS . APP_DIR . DS);
			  define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
		 }
	}
	if (!include(CORE_PATH . 'cake' . DS . 'bootstrap.php')) {
		trigger_error("Can't find CakePHP core.  Check the value of CAKE_CORE_INCLUDE_PATH in app/webroot/index.php.  It should point to the directory containing your " . DS . "cake core directory and your " . DS . "vendors root directory.", E_USER_ERROR);
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
   
    if(Context::read('network.url') && !defined('FULL_BASE_URL')) {
        define('FULL_BASE_URL', Context::read('network.url'));
    }

    define('SHELL_START_TIMESTAMP', date('Y-m-d H:i:s'));
    $Dispatcher= new Dispatcher();
    $Dispatcher->dispatch($argv[1]);
} else {
    echo $argv[0] . ': error: wrong parameter count (' . ($argc - 1) . ")\n";
    echo $usage;
}
?>