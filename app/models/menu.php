<?php

	class Menu {
		
		function getMainMenu($options) {
			$menuItems = array();
			$controller = $this->value($options, 'controller');
			$action = $this->value($options, 'action');
			
			if (isset($options['is_local'])) {
				if ($options['is_local'] === true) {
					$localUsername = $this->value($options, 'local_username');
					$menuItems = $this->getMainMenuForLocalUser($controller, $action, $localUsername);
				} else {
					$menuItems = $this->getMainMenuForRemoteUser($controller, $action);
				}
			} else {
				$menuItems = $this->getMainMenuForAnonymousUser($controller, $action, $this->getRegistrationType($options));
			}
			
			return $menuItems;
		}
		
		function getSubMenu($options) {
			$controller = $this->value($options, 'controller');
			$action = $this->value($options, 'action');
			
			if ($controller == 'Identities' && $action == 'social_stream') {
				$filter = $this->value($options, 'filter');
				$menuItems = $this->getFilterSubMenu($filter);
			} else {
				$localUsername = $this->value($options, 'local_username');
				$isOpenIDUser = $this->value($options, 'openid_user', false);
				$menuItems = $this->getSettingsSubMenu($controller, $action, $localUsername, $isOpenIDUser);
			}
			return $menuItems;
		}
		
		private function getFilterSubMenu($filter) {
			$menuItems[] = new MenuItem('All', '', $filter == 'all');
			$menuItems[] = new MenuItem('Photo', '', $filter == 'photo');
			$menuItems[] = new MenuItem('Video', '', $filter == 'video');
			$menuItems[] = new MenuItem('Audio', '', $filter == 'audio');
			$menuItems[] = new MenuItem('Link', '', $filter == 'link');
			$menuItems[] = new MenuItem('Text', '', $filter == 'text');
			$menuItems[] = new MenuItem('Micropublish', '', $filter == 'micropublish');
			$menuItems[] = new MenuItem('Events', '', $filter == 'event');
			$menuItems[] = new MenuItem('Documents', '', $filter == 'document');
			$menuItems[] = new MenuItem('Locations', '', $filter == 'location');
			
			return $menuItems;
		}
		
		private function getMainMenuForAnonymousUser($controller, $action, $registrationType) {
			$menuItems[] = new SocialStreamMenuItem($controller, $action);
			
			if ($registrationType == 'all') {
				$menuItems[] = new RegisterMenuItem($controller, $action);
			}
			
			return $menuItems;
		}
		
		private function getMainMenuForLocalUser($controller, $action, $localUsername) {
			$menuItems[] = new SocialStreamMenuItem($controller, $action);
			$menuItems[] = new MyProfileMenuItem($controller, $action, $localUsername);
			$menuItems[] = new MyContactsMenuItem($controller, $localUsername);
			$menuItems[] = new SettingsMenuItem($controller, $action, $localUsername);
			
			return $menuItems;
		}
		
		private function getMainMenuForRemoteUser($controller, $action) {
			$menuItems[] = new SocialStreamMenuItem($controller, $action);
			$menuItems[] = new MyProfileMenuItem($controller, $action);
			
			return $menuItems;
		}
		
		private function getRegistrationType($options) {
			$registrationType = '';
			
			if (!isset($options['registration_type'])) {
				$registrationType = NOSERUB_REGISTRATION_TYPE;
			} else {
				$registrationType = $options['registration_type'];
			}
			
			return $registrationType;
		}
		
		private function getSettingsSubMenu($controller, $action, $localUsername, $isOpenIDUser) {
			$link = '/' . $localUsername . '/settings/';
			
			$menuItems[] = new MenuItem('Profile', $link . 'profile/', $controller == 'Identities' && $action == 'profile_settings');
			$menuItems[] = new MenuItem('Accounts', $link . 'accounts/', $controller == 'Accounts');
			$menuItems[] = new MenuItem('Privacy', $link . 'privacy/', $controller == 'Identities' && $action == 'privacy_settings');
			$menuItems[] = new MenuItem('Feeds', $link . 'feeds/', $controller == 'Syndications');
			$menuItems[] = new MenuItem('OpenID', $link . 'openid/', $controller == 'OpenidSites');
			
			if (!$isOpenIDUser) {
				$menuItems[] = new MenuItem('Password', $link . 'password/', $controller == 'Identities' && $action == 'password_settings');
			}
			
			$menuItems[] = new MenuItem('Delete account', $link . 'account/', $controller == 'Identities' && $action == 'account_settings');
			
			return $menuItems;
		}
		
		private function value($options, $key, $defaultValue = '') {
			return isset($options[$key]) ? $options[$key] : $defaultValue;
		}
	}
	
	class MenuItem {
		private $label = null;
		private $link = null;
		private $isActive = false;
		
		function __construct($label, $link, $isActive) {
			$this->label = $label;
			$this->link = $link;
			$this->isActive = $isActive;
		}
		
		function getLabel() {
			return $this->label;
		}
		
		function getLink() {
			return $this->link;
		}
		
		function isActive() {
			return $this->isActive;
		}
	}
	
	class MyContactsMenuItem extends MenuItem {
		private $controller = null;
		
		function __construct($controller, $localUsername) {
			parent::__construct('My Contacts', '/' . $localUsername . '/contacts/', false);
			$this->controller = $controller;
		}
		
		function isActive() {
			if ($this->controller == 'Contacts') {
				return true;
			}
			
			return false;
		}
	}
	
	class MyProfileMenuItem extends MenuItem {
		private $controller = null;
		private $action = null;
		
		function __construct($controller, $action, $localUsername = null) {
			// TODO adding link for profile of remote user
			$link = ($localUsername == null) ? '' : '/' . $localUsername . '/';
			
			parent::__construct('My Profile', $link, false);
			$this->controller = $controller;
			$this->action = $action;
		}
		
		function isActive() {
			if ($this->controller == 'Identities' && $this->action == 'index') {
				return true;
			}
			
			return false;
		}
	}
	
	class RegisterMenuItem extends MenuItem {
		private $controller = null;
		private $action = null;
		
		function __construct($controller, $action) {
			parent::__construct('Add me!', '/pages/register/', false);
			$this->controller = $controller;
			$this->action = $action;
		}
		
		function isActive() {
			if ($this->controller == 'Identities' && $this->action == 'register') {
				return true;
			}
			
			return false;
		}
	}
	
	class SettingsMenuItem extends MenuItem {
		private $controller = null;
		private $action = null;
		
		function __construct($controller, $action, $localUsername) {
			parent::__construct('Settings', '/' . $localUsername . '/settings/', false);
			$this->controller = $controller;
			$this->action = $action;
		}
		
		function isActive() {
			$controllers = array('Accounts', 'OpenidSites', 'Syndications');
			
			if (in_array($this->controller, $controllers)) {
				return true;
			}
			
			if ($this->controller == 'Identities') {
				$identityActions = array('account_settings', 'password_settings', 'privacy_settings', 'profile_settings');
					
				if (in_array($this->action, $identityActions)) {
					return true;
				}
			}
			
			return false;
		}
	}
	
	class SocialStreamMenuItem extends MenuItem {
		private $controller = null;
		private $action = null;
		
		function __construct($controller, $action) {
			parent::__construct('Social Stream', '/social_stream/', false);
			$this->controller = $controller;
			$this->action = $action;
		}
		
		function isActive() {
			if ($this->controller == 'Identities' && $this->action == 'social_stream') {
				return true;
			}
			
			return false;
		}
	}
?>