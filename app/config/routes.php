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
#Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
Router::connect('/', array('controller' => 'identities', 'action' => 'social_stream'));
Router::connect('/social_stream/:filter/:output/', array('controller' => 'identities', 'action' => 'social_stream'));
    
/**
 * Then we connect url '/test' to our test controller. This is helpfull in
 * developement.
 */
Router::connect('/pages/login/', array('controller' => 'identities', 'action' => 'login'));
Router::connect('/pages/logout/:security_token/', array('controller' => 'identities', 'action' => 'logout'));
Router::connect('/pages/register/', array('controller' => 'registration', 'action' => 'register'));
Router::connect('/pages/register/withopenid/', array('controller' => 'registration', 'action' => 'register_with_openid_step_1'));
Router::connect('/pages/register/withopenid/step2', array('controller' => 'registration', 'action' => 'register_with_openid_step_2'));
Router::connect('/pages/register/thanks/', array('controller' => 'registration', 'action' => 'register_thanks'));
Router::connect('/pages/verify/:hash/', array('controller' => 'registration', 'action' => 'verify'));
Router::connect('/pages/account/deleted/', array('controller' => 'identities', 'action' => 'account_deleted'));
Router::connect('/pages/security_check/', array('controller' => 'pages', 'action' => 'security_check'));
Router::connect('/pages/yadis.xrdf', array('controller' => 'identities', 'action' => 'yadis'));
Router::connect('/pages/oauth/request_token', array('controller' => 'oauth', 'action' => 'request_token'));
Router::connect('/pages/oauth/access_token', array('controller' => 'oauth', 'action' => 'access_token'));
Router::connect('/pages/oauth/authorize', array('controller' => 'oauth', 'action' => 'authorize'));

Router::connect('/api/:result_type/info/', array('controller' => 'identities', 'action' => 'api_info'));

// OAuth-enabled API methods
Router::connect('/api/:result_type/locations/last/', array('controller' => 'identities', 'action' => 'api_get_last_location'));
Router::connect('/api/:result_type/locations/set/*', array('controller' => 'locations', 'action' => 'api_set'));
Router::connect('/api/:result_type/locations/add/', array('controller' => 'locations', 'action' => 'api_add'));
Router::connect('/api/:result_type/locations/', array('controller' => 'locations', 'action' => 'api_get'));
Router::connect('/api/:result_type/vcard/', array('controller' => 'identities', 'action' => 'api_get'));
Router::connect('/api/:result_type/feeds/', array('controller' => 'syndications', 'action' => 'api_get'));
Router::connect('/api/:result_type/contacts/', array('controller' => 'contacts', 'action' => 'api_get'));
Router::connect('/api/:result_type/accounts/', array('controller' => 'accounts', 'action' => 'api_get'));

Router::connect('/api/:username/:api_hash/:result_type/locations/last/', array('controller' => 'identities', 'action' => 'api_get_last_location'));
Router::connect('/api/:username/:api_hash/:result_type/locations/set/*', array('controller' => 'locations', 'action' => 'api_set'));
Router::connect('/api/:username/:api_hash/:result_type/locations/add/', array('controller' => 'locations', 'action' => 'api_add'));
Router::connect('/api/:username/:api_hash/:result_type/locations/', array('controller' => 'locations', 'action' => 'api_get'));
Router::connect('/api/:username/:api_hash/:result_type/vcard/', array('controller' => 'identities', 'action' => 'api_get'));
Router::connect('/api/:username/:api_hash/:result_type/feeds/', array('controller' => 'syndications', 'action' => 'api_get'));
Router::connect('/api/:username/:api_hash/:result_type/contacts/', array('controller' => 'contacts', 'action' => 'api_get'));
Router::connect('/api/:username/:api_hash/:result_type/accounts/', array('controller' => 'accounts', 'action' => 'api_get'));

Router::connect('/auth/:action', array('controller' => 'auth'));

Router::connect('/search', array('controller' => 'searches', 'action' => 'index'));
Router::connect('/:username/network/:filter', array('controller' => 'contacts', 'action' => 'network'));
Router::connect('/:username/contacts/add/', array('controller' => 'contacts', 'action' => 'add'));
Router::connect('/:username/contacts/:contact_id/edit/', array('controller' => 'contacts', 'action' => 'edit'));
Router::connect('/:username/contacts/:contact_id/info/', array('controller' => 'contacts', 'action' => 'info'));
Router::connect('/:username/contacts/:contact_id/delete/:security_token', array('controller' => 'contacts', 'action' => 'delete'));
Router::connect('/:username/contacts/*/accounts/add/', array('controller' => 'accounts', 'action' => 'add'));
Router::connect('/:username/contacts/', array('controller' => 'contacts', 'action' => 'index'));
Router::connect('/:username/add/as/contact/:security_token/', array('controller' => 'contacts', 'action' => 'add_as_contact'));
Router::connect('/:username/xrds', array('controller' => 'identities', 'action' => 'xrds'));
Router::connect('/:username/feeds/*', array('controller' => 'syndications', 'action' => 'feed'));
Router::connect('/:username/messages/new/*', array('controller' => 'identities', 'action' => 'send_message'));
Router::connect('/:username/subscribe', array('controller' => 'omb', 'action' => 'subscribe'));
Router::connect('/:username/callback', array('controller' => 'omb', 'action' => 'callback'));

Router::connect('/:username/settings/display/', array('controller' => 'identities', 'action' => 'display_settings'));
Router::connect('/:username/settings/password/', array('controller' => 'identities', 'action' => 'password_settings'));
Router::connect('/:username/settings/privacy/', array('controller' => 'identities', 'action' => 'privacy_settings'));
Router::connect('/:username/settings/account/', array('controller' => 'account_settings', 'action' => 'index'));
Router::connect('/:username/settings/account/export/:security_token/', array('controller' => 'account_settings', 'action' => 'export'));
Router::connect('/:username/settings/account/import_data/:security_token/', array('controller' => 'account_settings', 'action' => 'import_data'));
Router::connect('/:username/settings/account/import/', array('controller' => 'account_settings', 'action' => 'import'));
Router::connect('/:username/settings/account/redirect/', array('controller' => 'account_settings', 'action' => 'redirect_url'));
Router::connect('/:username/settings/openid/', array('controller' => 'openid_sites', 'action' => 'index'));

Router::connect('/:username/settings/feeds/add/', array('controller' => 'syndications', 'action' => 'add'));
Router::connect('/:username/settings/feeds/:syndication_id/delete/:security_token/', array('controller' => 'syndications', 'action' => 'delete'));
Router::connect('/:username/settings/feeds/', array('controller' => 'syndications', 'action' => 'index'));

Router::connect('/:username/settings/locations/add/', array('controller' => 'locations', 'action' => 'add'));
Router::connect('/:username/settings/locations/:location_id/delete/:security_token', array('controller' => 'locations', 'action' => 'delete'));
Router::connect('/:username/settings/locations/:location_id/edit/', array('controller' => 'locations', 'action' => 'edit'));
Router::connect('/:username/settings/locations/', array('controller' => 'locations', 'action' => 'index'));

Router::connect('/:username/settings/accounts/add/', array('controller' => 'accounts', 'action' => 'add_step_1'));
Router::connect('/:username/settings/accounts/add/preview/', array('controller' => 'accounts', 'action' => 'add_step_2_preview'));
Router::connect('/:username/settings/accounts/add/friends/', array('controller' => 'accounts', 'action' => 'add_step_3_friends'));
Router::connect('/:username/settings/accounts/*/edit/', array('controller' => 'accounts', 'action' => 'edit'));
Router::connect('/:username/settings/accounts/:account_id/delete/:security_token/', array('controller' => 'accounts', 'action' => 'delete'));
Router::connect('/:username/settings/accounts/', array('controller' => 'accounts', 'action' => 'index'));

Router::connect('/:username/settings/oauth/add', array('controller' => 'oauth_consumers', 'action' => 'add'));
Router::connect('/:username/settings/oauth/:consumer_id/delete/:security_token', array('controller' => 'oauth_consumers', 'action' => 'delete'));
Router::connect('/:username/settings/oauth/:consumer_id/edit/', array('controller' => 'oauth_consumers', 'action' => 'edit'));
Router::connect('/:username/settings/oauth', array('controller' => 'oauth_consumers', 'action' => 'index'));

Router::connect('/:username/settings/*', array('controller' => 'identities', 'action' => 'profile_settings'));

Router::connect('/:username/:filter', array('controller' => 'identities', 'action' => 'index'));

Router::connect('/jobs/:admin_hash/entries/update/', array('controller' => 'entries', 'action' => 'jobs_update'));
Router::connect('/jobs/:admin_hash/sync/identity/:identity_id/', array('controller' => 'identities', 'action' => 'jobs_sync'));
Router::connect('/jobs/:admin_hash/sync/all/', array('controller' => 'identities', 'action' => 'jobs_sync_all'));
Router::connect('/jobs/:admin_hash/system/update/', array('controller' => 'admins', 'action' => 'system_update'));
Router::connect('/jobs/:admin_hash/tests/:action/*', array('controller' => 'tests'));
Router::connect('/jobs/:admin_hash/xmpp/start', array('controller' => 'xmpp', 'action' => 'shell_run'));

Router::connect('/jobs/cron/:cron_hash/identities/sync/all/', array('controller' => 'identities', 'action' => 'cron_sync_all'));
Router::connect('/jobs/cron/:cron_hash/cache/feed/refresh/', array('controller' => 'entries', 'action' => 'cron_update'));

/**
 * Shell routes that can only be accessed through the shell_dispatcher
 */ 
Router::connect('/identities/sync/all/', array('controller' => 'identities', 'action' => 'shell_sync_all'));
Router::connect('/cache/feed/refresh/', array('controller' => 'entries', 'action' => 'shell_update'));
Router::connect('/cache/feed/upload/', array('controller' => 'syndications', 'action' => 'shell_upload'));