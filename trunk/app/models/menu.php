<?php

	class Menu extends AppModel {
		var $useTable = false;
		
		function getMainMenu($options) {
			$controller = isset($options['controller']) ? $options['controller'] : '';
			$action = isset($options['action']) ? $options['action'] : '';
			
			$menuItems[] = new MenuItem('Social Stream', '/social_stream/', $this->shouldSocialStreamBeActivated($controller, $action));

			if (isset($options['is_local'])) {
				if ($options['is_local'] === false) {
					$menuItems[] = new MenuItem('My Profile', '', $this->shouldMyProfileBeActivated($controller, $action));
				} else {
					$menuItems[] = new MenuItem('My Profile', '', $this->shouldMyProfileBeActivated($controller, $action));
					$menuItems[] = new MenuItem('My Contacts', '', false);
					$menuItems[] = new MenuItem('Settings', '', $this->shouldSettingsBeActivated($controller, $action));
				}
			} else {
				if (!isset($options['registration_type'])) {
					$registrationType = NOSERUB_REGISTRATION_TYPE;
				} else {
					$registrationType = $options['registration_type'];
				}
				
				if ($registrationType == 'all') {
					$menuItems[] = new MenuItem('Add me!', '/pages/register/', $this->shouldRegisterBeActivated($controller, $action));
				}
			}
			
			return $menuItems;
		}
		
		private function shouldMyProfileBeActivated($controller, $action) {
			if ($controller == 'Identities' && $action == 'index') {
				return true;
			}
			
			return false;
		}
		
		private function shouldRegisterBeActivated($controller, $action) {
			if ($controller == 'Identities' && $action == 'register') {
				return true;
			}
			
			return false;
		}
		
		private function shouldSettingsBeActivated($controller, $action) {
			$controllers = array('Accounts', 'OpenidSites', 'Syndications');
			
			if (in_array($controller, $controllers)) {
				return true;
			}
				
			if ($controller == 'Identities') {
				$identityActions = array('account_settings', 'password_settings', 'privacy_settings', 'profile_settings');
					
				if (in_array($action, $identityActions)) {
					return true;
				}
			}
			
			return false;
		}
		
		private function shouldSocialStreamBeActivated($controller, $action) {
			if ($controller == 'Identities' && $action == 'social_stream') {
				return true;
			}
			
			return false;
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
?>