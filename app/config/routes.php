<?php

Router::connect('/', array('controller' => 'pages', 'action' => 'home'));

Router::connect('/jobs/send_mail/', array('controller' => 'mails', 'action' => 'send'));

Router::connect('/widgets/:action', array('controller' => 'widgets'));

Router::connect('/groups/entry/:slug/*', array('controller' => 'entries', 'action' => 'view'));
Router::connect('/groups/:action', array('controller' => 'groups'));
Router::connect('/comments/:action', array('controller' => 'comments'));
Router::connect('/pages/switch/language/*', array('controller' => 'identities', 'action' => 'switch_language'));
Router::connect('/events/entry/:slug/*', array('controller' => 'entries', 'action' => 'view'));
Router::connect('/events/:action', array('controller' => 'events'));
Router::connect('/locations/entry/:slug/*', array('controller' => 'entries', 'action' => 'view'));
Router::connect('/locations/:action', array('controller' => 'locations'));

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
Router::connect('/pages/omb/:action', array('controller' => 'omb_local_service'));
Router::connect('/pages/password/recovery/*', array('controller' => 'identities', 'action' => 'password_recovery'));
Router::connect('/pages/password/set/*', array('controller' => 'identities', 'action' => 'password_recovery_set'));

Router::connect('/oauth/:action', array('controller' => 'oauth'));
Router::connect('/contacts/:action', array('controller' => 'contacts'));
Router::connect('/messages/:action', array('controller' => 'messages'));

Router::connect('/api/direct_messages', array('plugin' => 'api', 'controller' => 'direct_messages', 'action' => 'index'));
Router::connect('/api/locations/', array('plugin' => 'api', 'controller' => 'locations', 'action' => 'index'));

Router::connect('/auth/:action', array('controller' => 'auth'));

Router::connect('/search', array('controller' => 'searches', 'action' => 'index'));
Router::connect('/entry/add/*', array('controller' => 'entries', 'action' => 'add'));
Router::connect('/entry/mark/*', array('controller' => 'entries', 'action' => 'mark'));
Router::connect('/entry/*', array('controller' => 'entries', 'action' => 'view'));

Router::connect('/admins/ads/:action', array('controller' => 'ads'));
Router::connect('/admins/:action', array('controller' => 'admins'));
Router::connect('/networks/:action', array('controller' => 'networks'));

Router::connect('/activities/', array('controller' => 'identities', 'action' => 'social_stream'));

Router::connect('/settings/display/', array('controller' => 'identities', 'action' => 'display_settings'));
Router::connect('/settings/password/', array('controller' => 'identities', 'action' => 'password_settings'));
Router::connect('/settings/privacy/', array('controller' => 'identities', 'action' => 'privacy_settings'));
Router::connect('/settings/account/', array('controller' => 'account_settings', 'action' => 'index'));
Router::connect('/settings/account/export/:security_token/', array('controller' => 'account_settings', 'action' => 'export'));
Router::connect('/settings/account/import_data/:security_token/', array('controller' => 'account_settings', 'action' => 'import_data'));
Router::connect('/settings/account/import/', array('controller' => 'account_settings', 'action' => 'import'));
Router::connect('/settings/account/redirect/', array('controller' => 'account_settings', 'action' => 'redirect_url'));
Router::connect('/settings/openid/', array('controller' => 'openid_sites', 'action' => 'index'));
Router::connect('/settings/twitter/', array('controller' => 'twitter_accounts', 'action' => 'index'));
Router::connect('/settings/twitter/delete/:security_token/', array('controller' => 'twitter_accounts', 'action' => 'delete'));
Router::connect('/settings/api', array('controller' => 'api_users', 'action' => 'index'));

Router::connect('/settings/accounts/add/', array('controller' => 'accounts', 'action' => 'add'));
Router::connect('/settings/accounts/edit/*', array('controller' => 'accounts', 'action' => 'edit'));
Router::connect('/settings/accounts/delete/', array('controller' => 'accounts', 'action' => 'delete'));
Router::connect('/settings/accounts/', array('controller' => 'accounts', 'action' => 'settings'));

Router::connect('/settings/oauth/add', array('controller' => 'oauth_consumers', 'action' => 'add'));
Router::connect('/settings/oauth/:consumer_id/delete/:security_token', array('controller' => 'oauth_consumers', 'action' => 'delete'));
Router::connect('/settings/oauth/:consumer_id/edit/', array('controller' => 'oauth_consumers', 'action' => 'edit'));
Router::connect('/settings/oauth', array('controller' => 'oauth_consumers', 'action' => 'index'));

Router::connect('/settings/*', array('controller' => 'identities', 'action' => 'profile_settings'));

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
Router::connect('/jobs/shell/peers/sync/', array('controller' => 'networks', 'action' => 'shell_sync')); # deprecated
Router::connect('/jobs/shell/peers/poll/', array('controller' => 'networks', 'action' => 'shell_poll')); # deprecated
Router::connect('/jobs/shell/networks/sync/', array('controller' => 'networks', 'action' => 'shell_sync'));
Router::connect('/jobs/shell/networks/poll/', array('controller' => 'networks', 'action' => 'shell_poll'));

/**
 * All routes starting with "/:username/" need to be put last
 */
Router::connect('/:username/activities/', array('controller' => 'entries', 'action' => 'profile'));
Router::connect('/:username/groups/', array('controller' => 'groups', 'action' => 'profile'));
Router::connect('/:username/locations/', array('controller' => 'locations', 'action' => 'profile'));
Router::connect('/:username/events/', array('controller' => 'events', 'action' => 'profile'));
Router::connect('/:username/networks/', array('controller' => 'networks', 'action' => 'profile'));
Router::connect('/:username/contacts/add/', array('controller' => 'contacts', 'action' => 'add'));
Router::connect('/:username/contacts/:contact_id/edit/', array('controller' => 'contacts', 'action' => 'edit'));
Router::connect('/:username/contacts/:contact_id/info/', array('controller' => 'contacts', 'action' => 'info'));
Router::connect('/:username/contacts/:contact_id/delete/:security_token', array('controller' => 'contacts', 'action' => 'delete'));
Router::connect('/:username/contacts/*/accounts/add/', array('controller' => 'accounts', 'action' => 'add'));
Router::connect('/:username/contacts/', array('controller' => 'contacts', 'action' => 'index'));
Router::connect('/:username/add/as/contact/:security_token/', array('controller' => 'contacts', 'action' => 'add_as_contact'));
Router::connect('/:username/remove/contact/:security_token/', array('controller' => 'contacts', 'action' => 'remove_contact'));
Router::connect('/:username/xrds', array('controller' => 'identities', 'action' => 'xrds'));
Router::connect('/:username/messages/new/*', array('controller' => 'identities', 'action' => 'send_message'));
Router::connect('/:username/subscribe', array('controller' => 'omb_subscriptions', 'action' => 'subscribe'));
Router::connect('/:username/callback', array('controller' => 'omb_subscriptions', 'action' => 'callback'));
Router::connect('/:username/favorites/', array('controller' => 'identities', 'action' => 'favorites'));
Router::connect('/:username/comments/', array('controller' => 'identities', 'action' => 'comments'));
Router::connect('/:username/', array('controller' => 'identities', 'action' => 'profile'));

Router::parseExtensions('json', 'xml');