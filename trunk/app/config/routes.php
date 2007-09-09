<?php
/* SVN FILE: $Id: routes.php 4410 2007-02-02 13:31:21Z phpnut $ */
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
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
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision$
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.thtml)...
 */
	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));

/**
 * Then we connect url '/test' to our test controller. This is helpfull in
 * developement.
 */
	Router::connect('/tests/:action/*', array('controller' => 'tests'));
	
	Router::connect('/pages/login/', array('controller' => 'identities', 'action' => 'login'));
	Router::connect('/pages/logout/', array('controller' => 'identities', 'action' => 'logout'));
	Router::connect('/pages/register/', array('controller' => 'identities', 'action' => 'register'));
	Router::connect('/pages/register/thanks/', array('controller' => 'identities', 'action' => 'register_thanks'));
	Router::connect('/pages/verify/:username/:hash/', array('controller' => 'identities', 'action' => 'verify'));
      
    Router::connect('/:username/network/:filter', array('controller' => 'contacts', 'action' => 'network'));

    Router::connect('/:username/accounts/add/service/', array('controller' => 'accounts', 'action' => 'add_step_2_service'));
    Router::connect('/:username/accounts/add/feed/', array('controller' => 'accounts', 'action' => 'add_step_2_feed'));
    Router::connect('/:username/accounts/add/preview/', array('controller' => 'accounts', 'action' => 'add_step_3_preview'));
    Router::connect('/:username/accounts/add/friends/', array('controller' => 'accounts', 'action' => 'add_step_4_friends'));
    Router::connect('/:username/accounts/add/', array('controller' => 'accounts', 'action' => 'add_step_1'));
    Router::connect('/:username/accounts/*/edit/', array('controller' => 'accounts', 'action' => 'edit'));
    Router::connect('/:username/accounts/*/delete', array('controller' => 'accounts', 'action' => 'delete'));
    Router::connect('/:username/accounts/', array('controller' => 'accounts', 'action' => 'index'));
    
    Router::connect('/:username/contacts/add/', array('controller' => 'contacts', 'action' => 'add'));
    Router::connect('/:username/contacts/*/edit/', array('controller' => 'contacts', 'action' => 'edit'));
    Router::connect('/:username/contacts/*/delete/', array('controller' => 'contacts', 'action' => 'delete'));
    Router::connect('/:username/contacts/*/accounts/add/', array('controller' => 'accounts', 'action' => 'add'));
    Router::connect('/:username/contacts/', array('controller' => 'contacts', 'action' => 'index'));
    
    Router::connect('/:username/:filter', array('controller' => 'identities', 'action' => 'index'));
    
    Router::connect('/jobs/:admin_hash/sync/identity/:identity_id/', array('controller' => 'accounts', 'action' => 'jobs_sync'));
    Router::connect('/jobs/:admin_hash/sync/all/', array('controller' => 'accounts', 'action' => 'jobs_sync_all'));
    Router::connect('/jobs/:admin_hash/system/update/', array('controller' => 'admins', 'action' => 'system_update'));