<?php
/* SVN FILE: $Id: bootstrap.php 4410 2007-02-02 13:31:21Z phpnut $ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.app.config
 * @since			CakePHP(tm) v 0.10.8.2117
 * @version			$Revision$
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 *
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php is loaded
 * This is an application wide file to load any function that is not used within a class define.
 * You can also use this to include or require any files in your application.
 *
 */
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * $modelPaths = array('full path to models', 'second full path to models', 'etc...');
 * $viewPaths = array('this path to views', 'second full path to views', 'etc...');
 * $controllerPaths = array('this path to controllers', 'second full path to controllers', 'etc...');
 *
 */
//EOF

if(file_exists(APP . '/config/noserub.php')) {
    require_once 'noserub.php';
} else {
    die('noserub.php not found!');
}

define('NOSERUB_VALID_USERNAME', '/^[\w.-_]+$/ism');
#define('NOSERUB_VALID_LOCAL_USERNAME', '/^[\da-zA-Z-\.\_]+@' . NOSERUB_DOMAIN . '$/');

# to exclude pages, tests and jobs is essential here, because else, 
# the routes would not be working. excluding the others is
# just a precaution for avoiding confusions.
define('NOSERUB_RESERVED_USERNAMES', 'pages,jobs,tests,noserub,auth,login,register,social_stream');

# temporary constant for development purposes
# TODO remove constant NOSERUB_ALLOW_REMOTE_LOGIN when remote login is working
define('NOSERUB_ALLOW_REMOTE_LOGIN', false);

/**
 * Static files directory
 * @name STATIC_DIR
 */ 
$static_dir = APP . WEBROOT_DIR . DS . 'static' . DS;
define('STATIC_DIR', $static_dir);

# this is no real data for bootstrap.php, but I figure, "functions.php" isn't
# suitable in /app/config/ either. So, as long as I don't know what to do with
# it, I will leave it here.
function sort_items($a, $b) {
	return $a['datetime'] < $b['datetime'];
}
?>