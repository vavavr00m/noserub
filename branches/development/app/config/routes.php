<?php

Router::connect('/', array('controller' => 'pages', 'action' => 'home'));
Router::connect('/social_stream/:filter/:output/', array('controller' => 'identities', 'action' => 'social_stream'));

Router::connect('/jobs/send_mail/', array('controller' => 'mails', 'action' => 'send'));

Router::connect('/widgets/:action', array('controller' => 'widgets'));

Router::connect('/groups/:action', array('controller' => 'groups'));

Router::connect('/pages/switch/language/*', array('controller' => 'identities', 'action' => 'switch_language'));
Router::connect('/pages/favorites/', array('controller' => 'identities', 'action' => 'favorites'));
Router::connect('/pages/comments/', array('controller' => 'identities', 'action' => 'comments'));

Router::connect('/pages/login/', array('controller' => 'identities', 'action' => 'login'));
Router::connect('/pages/login/openid', array('controller' => 'identities', 'action' => 'login_with_openid'));
Router::connect('/pages/logout/:security_token/', array('controller' => 'identities', 'action' => 'logout'));
Router::connect('/pages/register/', array('controller' => 'registration', 'action' => 'register'));
Router::connect('/pages/register/withopenid/', array('controller' => 'registration', 'action' => 'register_with_openid_step_1'));
Router::connect('/pages/register/withopenid/step2', array('controller' => 'registration', 'action' => 'register_with_openid_step_2'));
Router::connect('/pages/register/thanks/', array('controller' => 'registration', 'action' => 'register_thanks'));
Router::connect('/pages/verify/:hash/', array('controller' => 'registration', 'action' => 'verify'));
Router::connect('/pages/account/deleted/', array('controller' => 'identities', 'action' => 'account_deleted'));
Router::connect('/pages/security_check/', array('controller' => 'pages', 'action' => 'security_check'));
Router::connect('/pages/yadis.xrdf', array('controller' => 'identities', 'action' => 'yadis'));
Router::connect('/pages/oauth/:action', array('controller' => 'oauth'));
Router::connect('/pages/omb/:action', array('controller' => 'omb_local_service'));
Router::connect('/pages/password/recovery/*', array('controller' => 'identities', 'action' => 'password_recovery'));
Router::connect('/pages/password/set/*', array('controller' => 'identities', 'action' => 'password_recovery_set'));

Router::connect('/api/:result_type/info/', array('plugin' => 'api', 'controller' => 'system_api', 'action' => 'info'));
Router::connect('/api/:result_type/comments/', array('plugin' => 'api', 'controller' => 'comments_api', 'action' => 'get_comments'));
Router::connect('/api/:result_type/favorites/', array('plugin' => 'api', 'controller' => 'favorites_api', 'action' => 'get_favorites'));

// OAuth-enabled API methods
Router::connect('/api/:result_type/locations/last/', array('plugin' => 'api', 'controller' => 'locations_api', 'action' => 'get_last_location'));
Router::connect('/api/:result_type/locations/set/*', array('plugin' => 'api', 'controller' => 'locations_api', 'action' => 'set_location'));
Router::connect('/api/:result_type/locations/add/', array('plugin' => 'api', 'controller' => 'locations_api', 'action' => 'add_location'));
Router::connect('/api/:result_type/locations/', array('plugin' => 'api', 'controller' => 'locations_api', 'action' => 'get_locations'));
Router::connect('/api/:result_type/vcard/', array('plugin' => 'api', 'controller' => 'identities_api', 'action' => 'get_vcard'));
Router::connect('/api/:result_type/feeds/', array('plugin' => 'api', 'controller' => 'syndications_api', 'action' => 'get_feeds'));
Router::connect('/api/:result_type/contacts/', array('plugin' => 'api', 'controller' => 'contacts_api', 'action' => 'get_contacts'));
Router::connect('/api/:result_type/accounts/', array('plugin' => 'api', 'controller' => 'accounts_api', 'action' => 'get_accounts'));

Router::connect('/api/:username/:api_hash/:result_type/locations/last/', array('plugin' => 'api', 'controller' => 'locations_api', 'action' => 'get_last_location'));
Router::connect('/api/:username/:api_hash/:result_type/locations/set/*', array('plugin' => 'api', 'controller' => 'locations_api', 'action' => 'set_location'));
Router::connect('/api/:username/:api_hash/:result_type/locations/add/', array('plugin' => 'api', 'controller' => 'locations_api', 'action' => 'add_location'));
Router::connect('/api/:username/:api_hash/:result_type/locations/', array('plugin' => 'api', 'controller' => 'locations_api', 'action' => 'get_locations'));
Router::connect('/api/:username/:api_hash/:result_type/vcard/', array('plugin' => 'api', 'controller' => 'identities_api', 'action' => 'get_vcard'));
Router::connect('/api/:username/:api_hash/:result_type/feeds/', array('plugin' => 'api', 'controller' => 'syndications_api', 'action' => 'get_feeds'));
Router::connect('/api/:username/:api_hash/:result_type/contacts/', array('plugin' => 'api', 'controller' => 'contacts_api', 'action' => 'get_contacts'));
Router::connect('/api/:username/:api_hash/:result_type/accounts/', array('plugin' => 'api', 'controller' => 'accounts_api', 'action' => 'get_accounts'));

Router::connect('/auth/:action', array('controller' => 'auth'));

Router::connect('/search', array('controller' => 'searches', 'action' => 'index'));
Router::connect('/entry/*', array('controller' => 'entries', 'action' => 'view'));

Router::connect('/admins/:action', array('controller' => 'admins'));
Router::connect('/networks/:action', array('controller' => 'networks'));

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
Router::connect('/:username/subscribe', array('controller' => 'omb_subscriptions', 'action' => 'subscribe'));
Router::connect('/:username/callback', array('controller' => 'omb_subscriptions', 'action' => 'callback'));

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
Router::connect('/:username/settings/accounts/*/edit/', array('controller' => 'accounts', 'action' => 'edit'));
Router::connect('/:username/settings/accounts/:account_id/delete/:security_token/', array('controller' => 'accounts', 'action' => 'delete'));
Router::connect('/:username/settings/accounts/', array('controller' => 'accounts', 'action' => 'index'));

Router::connect('/:username/settings/oauth/add', array('controller' => 'oauth_consumers', 'action' => 'add'));
Router::connect('/:username/settings/oauth/:consumer_id/delete/:security_token', array('controller' => 'oauth_consumers', 'action' => 'delete'));
Router::connect('/:username/settings/oauth/:consumer_id/edit/', array('controller' => 'oauth_consumers', 'action' => 'edit'));
Router::connect('/:username/settings/oauth', array('controller' => 'oauth_consumers', 'action' => 'index'));

Router::connect('/:username/settings/*', array('controller' => 'identities', 'action' => 'profile_settings'));

Router::connect('/:username/favorites/', array('controller' => 'identities', 'action' => 'favorites'));
Router::connect('/:username/comments/', array('controller' => 'identities', 'action' => 'comments'));
Router::connect('/:username/:filter', array('controller' => 'identities', 'action' => 'index'));

Router::connect('/jobs/cron/:cron_hash/identities/sync/all/', array('controller' => 'identities', 'action' => 'cron_sync_all'));
Router::connect('/jobs/cron/:cron_hash/cache/feed/refresh/', array('controller' => 'entries', 'action' => 'cron_update'));
Router::connect('/jobs/cron/:cron_hash/peers/sync/', array('controller' => 'networks', 'action' => 'cron_sync')); # deprecated
Router::connect('/jobs/cron/:cron_hash/peers/poll/', array('controller' => 'networks', 'action' => 'cron_poll')); # deprecated
Router::connect('/jobs/cron/:cron_hash/networks/sync/', array('controller' => 'networks', 'action' => 'cron_sync'));
Router::connect('/jobs/cron/:cron_hash/networks/poll/', array('controller' => 'networks', 'action' => 'cron_poll')); 


Router::connect('/jobs/:admin_hash/entries/update/', array('controller' => 'entries', 'action' => 'jobs_update'));
Router::connect('/jobs/:admin_hash/sync/identity/:identity_id/', array('controller' => 'identities', 'action' => 'jobs_sync'));
Router::connect('/jobs/:admin_hash/sync/all/', array('controller' => 'identities', 'action' => 'jobs_sync_all'));
Router::connect('/jobs/:admin_hash/system/update/', array('controller' => 'admins', 'action' => 'system_update'));
Router::connect('/jobs/:admin_hash/xmpp/start/', array('controller' => 'xmpp', 'action' => 'shell_run'));

/**
 * Shell routes that can only be accessed through the shell_dispatcher
 */ 
Router::connect('/jobs/shell/system/update/', array('controller' => 'admins', 'action' => 'shell_system_update')); 
Router::connect('/jobs/shell/identities/sync/all/', array('controller' => 'identities', 'action' => 'shell_sync_all'));
Router::connect('/jobs/shell/feeds/refresh/', array('controller' => 'entries', 'action' => 'shell_update'));
Router::connect('/jobs/shell/cache/feed/refresh/', array('controller' => 'entries', 'action' => 'shell_update'));
Router::connect('/jobs/shell/cache/feed/upload/', array('controller' => 'syndications', 'action' => 'shell_upload'));
Router::connect('/jobs/shell/peers/sync/', array('controller' => 'networks', 'action' => 'shell_sync')); # deprecated
Router::connect('/jobs/shell/peers/poll/', array('controller' => 'networks', 'action' => 'shell_poll')); # deprecated
Router::connect('/jobs/shell/networks/sync/', array('controller' => 'networks', 'action' => 'shell_sync'));
Router::connect('/jobs/shell/networks/poll/', array('controller' => 'networks', 'action' => 'shell_poll'));