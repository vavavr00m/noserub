<?php

class Menu {
	private $menuItems = null;
	
	public function __construct($menuItems) {
		$this->menuItems = $menuItems;
	}
	
	public function getActiveMenuItem() {
		foreach ($this->menuItems as $menuItem) {
			if ($menuItem->isActive()) {
				return $menuItem;
			}
		}
		
		return false;
	}
	
	public function getMenuItems() {
		return $this->menuItems;
	}
}

class MenuFactory {
	
	public function getMainMenu($options) {
		$menuItems = array();
		$controller = $this->value($options, 'controller');
		$action = $this->value($options, 'action');
		
		if(isset($options['is_local'])) {
			if($options['is_local'] === true) {
				$localUsername = $this->value($options, 'local_username');
				$menuItems = $this->getMainMenuForLocalUser($controller, $action, $localUsername);
			} else {
				$menuItems = $this->getMainMenuForRemoteUser($controller, $action);
			}
		} else {
			$menuItems = $this->getMainMenuForAnonymousUser($controller, $action, $this->getRegistrationType($options));
		}
		
		return new Menu($menuItems);
	}
	
	public function getSubMenu($options) {
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
		
		if (empty($menuItems)) {
			return false;
		}
		
		return new Menu($menuItems);
	}
	
	private function getFilterSubMenu($filter, $urlPart) {
		$menuItems[] = new MenuItem(__('Overview', true), $urlPart, $filter == 'all');
		$menuItems[] = new MenuItem(__('Photos', true), $urlPart.'photo/', $filter == 'photo');
		$menuItems[] = new MenuItem(__('Videos', true), $urlPart.'video/', $filter == 'video');
		$menuItems[] = new MenuItem(__('Audios', true), $urlPart.'audio/', $filter == 'audio');
		$menuItems[] = new MenuItem(__('Links', true), $urlPart.'link/', $filter == 'link');
		$menuItems[] = new MenuItem(__('Texts', true), $urlPart.'text/', $filter == 'text');
		$menuItems[] = new MenuItem(__('Micropublish', true), $urlPart.'micropublish/', $filter == 'micropublish');
		$menuItems[] = new MenuItem(__('Events', true), $urlPart.'event/', $filter == 'event');
		$menuItems[] = new MenuItem(__('Documents', true), $urlPart.'document/', $filter == 'document');
		$menuItems[] = new MenuItem(__('Locations', true), $urlPart.'location/', $filter == 'location');
		$menuItems[] = new MenuItem(__('NoseRub', true), $urlPart.'noserub/', $filter == 'noserub');
		
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
		$menuItems[] = new SearchMenuItem($controller, $action);
		
		if($registrationType == 'all') {
			$menuItems[] = new RegisterMenuItem($controller, $action);
		}
		
		return $menuItems;
	}
	
	private function getMainMenuForLocalUser($controller, $action, $localUsername) {
	    $menuItems[] = new MyContactsMenuItem($controller, $action, $localUsername);
		$menuItems[] = new SocialStreamMenuItem($controller, $action);
		$menuItems[] = new MyProfileMenuItem($controller, $action, $localUsername);
		$menuItems[] = new MyFavoritesMenuItem($controller, $action, $localUsername);
		
		return $menuItems;
	}
	
	private function getMainMenuForRemoteUser($controller, $action) {
		$menuItems[] = new SocialStreamMenuItem($controller, $action);
		$menuItems[] = new MyProfileMenuItem($controller, $action);
		
		return $menuItems;
	}
	
	private function getRegistrationType($options) {
		$registrationType = '';
		
		if(!isset($options['registration_type'])) {
			$registrationType = Configure::read('Noserub.registration_type');
		} else {
			$registrationType = $options['registration_type'];
		}
		
		return $registrationType;
	}
	
	private function getSettingsSubMenu($controller, $action, $localUsername, $isOpenIDUser) {
		$link = '/' . $localUsername . '/settings/';

		$menuItems[] = new MenuItem(__('Profile', true), $link . 'profile/', $controller == 'Identities' && $action == 'profile_settings');
		$menuItems[] = new MenuItem(__('Accounts', true), $link . 'accounts/', $controller == 'Accounts');
		$menuItems[] = new MenuItem(__('Locations', true), $link . 'locations/', $controller == 'Locations');
		$menuItems[] = new MenuItem(__('Display', true), $link . 'display/', $controller == 'Identities' && $action == 'display_settings');
		$menuItems[] = new MenuItem(__('Privacy', true), $link . 'privacy/', $controller == 'Identities' && $action == 'privacy_settings');
		$menuItems[] = new MenuItem(__('Feeds', true), $link . 'feeds/', $controller == 'Syndications');
		$menuItems[] = new MenuItem(__('OpenID', true), $link . 'openid/', $controller == 'OpenidSites');
		$menuItems[] = new MenuItem(__('OAuth', true), $link . 'oauth/', $controller == 'OauthConsumers');
		$menuItems[] = new MenuItem(__('OMB', true), $link . 'omb/', $controller == 'Omb');
		
		if (!$isOpenIDUser) {
			$menuItems[] = new MenuItem(__('Password & API', true), $link . 'password/', $controller == 'Identities' && $action == 'password_settings');
		} else {
			$menuItems[] = new MenuItem(__('API', true), $link . 'password/', $controller == 'Identities' && $action == 'password_settings');
		}
		
		$menuItems[] = new MenuItem(__('Manage', true), $link . 'account/', $controller == 'AccountSettings' && $action == 'index');
		
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
		$controllers = array('AccountSettings', 'Accounts', 'Locations', 'OauthConsumers', 'Omb', 'OpenidSites', 'Syndications');
		
		if (in_array($controller, $controllers)) {
			return true;
		}
		
		if ($controller == 'Identities') {
			$identityActions = array('profile_settings', 'privacy_settings', 'password_settings', 'display_settings');
			
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
	
	public function __construct($label, $link, $isActive) {
		$this->label = $label;
		$this->link = $link;
		$this->isActive = $isActive;
	}
	
	public function getLabel() {
		return $this->label;
	}
	
	public function getLink() {
		return $this->link;
	}
	
	public function isActive() {
		return $this->isActive;
	}
	
	public function isSettings() {
	    return false;
	}
}

class MyContactsMenuItem extends MenuItem {
	private $controller = null;
	private $action = null;
	
	public function __construct($controller, $action, $localUsername) {
		parent::__construct(__('With my Contacts', true), '/' . $localUsername . '/network/', false);
		$this->controller = $controller;
		$this->action = $action;
	}
	
	public function isActive() {
		if ($this->controller == 'Contacts') {
			return true;
		}
		
		return false;
	}
}

class MyProfileMenuItem extends MenuItem {
	private $controller = null;
	private $action = null;
	
	public function __construct($controller, $action, $localUsername = null) {
		// TODO adding link for profile of remote user
		$link = ($localUsername == null) ? '' : '/' . $localUsername . '/';
		
		parent::__construct(__('My Profile', true), $link, false);
		$this->controller = $controller;
		$this->action = $action;
	}
	
	public function isActive() {
		if ($this->controller == 'Identities' && $this->action == 'index') {
			return true;
		}
		
		return false;
	}
}

class MyFavoritesMenuItem extends MenuItem {
	private $controller = null;
	private $action = null;
	
	public function __construct($controller, $action, $localUsername = null) {
		// TODO adding link for profile of remote user
		$link = ($localUsername == null) ? '' : '/' . $localUsername . '/favorites/';
		
		parent::__construct(__('My Favorites', true), $link, false);
		$this->controller = $controller;
		$this->action = $action;
	}
	
	public function isActive() {
		if ($this->controller == 'Identities' && $this->action == 'favorites') {
			return true;
		}
		
		return false;
	}
}

class RegisterMenuItem extends MenuItem {
	private $controller = null;
	private $action = null;
	
	public function __construct($controller, $action) {
		parent::__construct(__('Register', true), '/pages/register/', false);
		$this->controller = $controller;
		$this->action = $action;
	}
	
	public function isActive() {
		if ($this->controller == 'Registration') {
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
	
	public function __construct($controller, $action, $localUsername) {
		parent::__construct(__('Settings', true), '/' . $localUsername . '/settings/', false);
		$this->controller = $controller;
		$this->action = $action;
	}
	
	public function isActive() {
		$controllers = array('AccountSettings', 'Locations', 'Accounts', 'OauthConsumers', 'Omb', 'OpenidSites', 'Syndications');
		
		if(in_array($this->controller, $controllers)) {
			return true;
		}
		
		if($this->controller == 'Identities') {
			$identityActions = array('password_settings', 'privacy_settings', 'profile_settings', 'display_settings');
				
			if(in_array($this->action, $identityActions)) {
				return true;
			}
		}
		
		return false;
	}
	
	public function isSettings() {
	    return true;
	}
}

class SocialStreamMenuItem extends MenuItem {
	private $controller = null;
	private $action = null;
	
	public function __construct($controller, $action) {
		parent::__construct(__('All Users', true), '/social_stream/', false);
		$this->controller = $controller;
		$this->action = $action;
	}
	
	public function isActive() {
		if ($this->controller == 'Identities' && $this->action == 'social_stream') {
			return true;
		}
		
		return false;
	}
}

class SearchMenuItem extends MenuItem {
	private $controller = null;
	private $action = null;
	
	public function __construct($controller, $action) {
		parent::__construct(__('Search', true), '/search/', false);
		$this->controller = $controller;
		$this->action = $action;
	}
	
	public function isActive() {
		if($this->controller == 'Searches') {
			return true;
		}
		
		return false;
	}
}