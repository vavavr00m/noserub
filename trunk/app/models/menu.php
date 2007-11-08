<?php

	class Menu extends AppModel {
		var $useTable = false;
		
		function getMainMenu($options) {
			$controller = isset($options['controller']) ? $options['controller'] : '';
			
			$menuItems[] = new MenuItem('Social Stream', '/social_stream/', $controller == 'Identities');

			if (isset($options['is_local'])) {
				if ($options['is_local'] === false) {
					$menuItems[] = new MenuItem('My Profile', '', false);
				} else {
					$menuItems[] = new MenuItem('My Profile', '', false);
					$menuItems[] = new MenuItem('My Contacts', '', false);
					$menuItems[] = new MenuItem('Settings', '', $this->shouldSettingsBeActivated($controller));
				}
			} else {
				if (!isset($options['registration_type'])) {
					$registrationType = NOSERUB_REGISTRATION_TYPE;
				} else {
					$registrationType = $options['registration_type'];
				}
				
				if ($registrationType == 'all') {
					$menuItems[] = new MenuItem('Add me!', '/pages/register/', false);
				}
			}
			
			return $menuItems;
		}
		
		private function shouldSettingsBeActivated($controller) {
			$controllers = array('Accounts', 'OpenidSites', 'Syndications');
			
			return in_array($controller, $controllers);
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