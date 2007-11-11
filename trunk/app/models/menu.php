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
			$menuItems = array();
			$controller = $this->value($options, 'controller');
			$action = $this->value($options, 'action');
			
			if ($this->showFilterSubMenu($controller, $action)) {
				$filter = $this->value($options, 'filter');
				
				if ($action == 'social_stream') {
					$menuItems = $this->getFilterSubMenuForSocialStream($filter);					
				} else {
					$localUsername = $this->value($options, 'local_username');

					if ($action == 'index') {
						$menuItems = $this->getFilterSubMenuForMyProfile($filter, $localUsername);
					} else {
						$menuItems = $this->getFilterSubMenuForNetwork($filter, $localUsername);
					}
				}
			} elseif ($this->showSettingsSubMenu($controller, $action)) {
				$localUsername = $this->value($options, 'local_username');
				$isOpenIDUser = $this->value($options, 'openid_user', false);
				$menuItems = $this->getSettingsSubMenu($controller, $action, $localUsername, $isOpenIDUser);
			}
			
			return $menuItems;
		}
		
		private function getFilterSubMenu($filter, $urlPart) {
			$menuItems[] = new MenuItem('All', $urlPart, $filter == 'all');
			$menuItems[] = new MenuItem('Photo', $urlPart.'photo/', $filter == 'photo');
			$menuItems[] = new MenuItem('Video', $urlPart.'video/', $filter == 'video');
			$menuItems[] = new MenuItem('Audio', $urlPart.'audio/', $filter == 'audio');
			$menuItems[] = new MenuItem('Link', $urlPart.'link/', $filter == 'link');
			$menuItems[] = new MenuItem('Text', $urlPart.'text/', $filter == 'text');
			$menuItems[] = new MenuItem('Micropublish', $urlPart.'micropublish/', $filter == 'micropublish');
			$menuItems[] = new MenuItem('Events', $urlPart.'event/', $filter == 'event');
			$menuItems[] = new MenuItem('Documents', $urlPart.'document/', $filter == 'document');
			$menuItems[] = new MenuItem('Locations', $urlPart.'location/', $filter == 'location');
			
			return $menuItems;
		}
		
		private function getFilterSubMenuForMyProfile($filter, $localUsername) {
			return $this->getFilterSubMenu($filter, '/'.$localUsername.'/');
		}
		
		private function getFilterSubMenuForNetwork($filter, $localUsername) {
			return $this->getFilterSubMenu($filter, '/'.$localUsername.'/network/');
		}
		
		private function getFilterSubMenuForSocialStream($filter) {
			return $this->getFilterSubMenu($filter, '/social_stream/');
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

		private function showFilterSubMenu($controller, $action) {
			if ($controller == 'Identities') {
				if ($action == 'social_stream' || $action == 'index') {
					return true;
				}
			}
			
			if ($controller == 'Contacts' && $action == 'network') {
				return true;
			}
			
			return false;
		}
		
		private function showSettingsSubMenu($controller, $action) {
			$controllers = array('Accounts', 'OpenidSites', 'Syndications');
			
			if (in_array($controller, $controllers)) {
				return true;
			}
			
			if ($controller == 'Identities') {
				$identityActions = array('profile_settings', 'privacy_settings', 'password_settings', 'account_settings');
				
				if (in_array($action, $identityActions)) {
					return true;
				}
			}
			
			return false;
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
			if ($this->controller == 'Identities') {
				$registerActions = array('register', 'register_with_openid_step_1', 'register_with_openid_step_2');
				
				if (in_array($this->action, $registerActions)) {
					return true;
				}
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