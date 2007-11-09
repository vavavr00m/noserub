<?php

	class MenuTest extends CakeTestCase {
		private $menu = null;
		
		function setUp() {
			$this->menu = new Menu();
		}
		
		// local user
		
		function testGetMainMenuForLocalUser() {
			$mainMenu = $this->menu->getMainMenu(array('is_local' => true));
			$this->assertEqual(4, count($mainMenu));
			$this->assertMenuForLocalUser($mainMenu, false, false, false, false);
		}
		
		function testGetMainMenuWithMyContactsSelected() {
			$mainMenu = $this->menu->getMainMenu(array('is_local' => true, 'controller' => 'Contacts'));
			$this->assertMenuForLocalUser($mainMenu, false, false, true, false);
		}
		
		function testGetMainMenuWithMyProfileSelected() {
			$mainMenu = $this->menu->getMainMenu(array('is_local' => true, 'controller' => 'Identities', 'action' => 'index'));
			$this->assertMenuForLocalUser($mainMenu, false, true, false, false);
		}
		
		function testGetMainMenuWithSettingsSelected() {
			$controllers = array('Accounts', 'OpenidSites', 'Syndications');
			
			foreach ($controllers as $controller) {
				$mainMenu = $this->menu->getMainMenu(array('is_local' => true, 'controller' => $controller));
				$this->assertMenuForLocalUser($mainMenu, false, false, false, true);
			}
			
			$identityActions = array('account_settings', 'password_settings', 'privacy_settings', 'profile_settings');
			
			foreach ($identityActions as $action) {
				$mainMenu = $this->menu->getMainMenu(array('is_local' => true, 'controller' => 'Identities', 'action' => $action));
				$this->assertMenuForLocalUser($mainMenu, false, false, false, true);
			}
		}
		
		function testGetMainMenuWithSocialStreamSelected() {
			$mainMenu = $this->menu->getMainMenu(array('is_local' => true, 'controller' => 'Identities', 'action' => 'social_stream'));
			$this->assertMenuForLocalUser($mainMenu, true, false, false, false);
		}		
		
		// remote user
		
		function testGetMainMenuForRemoteUser() {
			$mainMenu = $this->menu->getMainMenu(array('is_local' => false));
			$this->assertEqual(2, count($mainMenu));
			$this->assertMenuItem($mainMenu[0], 'Social Stream', false);
			$this->assertMenuItem($mainMenu[1], 'My Profile', false);
		}
		
		// anonymous user
		
		function testGetMainMenuForWebUsersWithRegistrationPossibility() {
			$mainMenu = $this->menu->getMainMenu(array('registration_type' => 'all'));
			$this->assertEqual(2, count($mainMenu));
			$this->assertMenuItem($mainMenu[0], 'Social Stream', false);
			$this->assertMenuItem($mainMenu[1], 'Add me!', false);
		}

		function testGetMainMenuWithRegisterSelected() {
			$mainMenu = $this->menu->getMainMenu(array('registration_type' => 'all', 'controller' => 'Identities', 'action' => 'register'));
			$this->assertMenuItem($mainMenu[0], 'Social Stream', false);
			$this->assertMenuItem($mainMenu[1], 'Add me!', true);
		}
		
		function testGetMainMenuForWebUsersWithoutRegistrationPossibility() {
			$mainMenu = $this->menu->getMainMenu(array('registration_type' => 'none'));
			$this->assertEqual(1, count($mainMenu));
			$this->assertMenuItem($mainMenu[0], 'Social Stream', false);
		}
		
		private function assertMenuForLocalUser($mainMenu, $socialStreamActive, $myProfileActive, $myContactsActive, $settingsActive) {
			$this->assertMenuItem($mainMenu[0], 'Social Stream', $socialStreamActive);
			$this->assertMenuItem($mainMenu[1], 'My Profile', $myProfileActive);
			$this->assertMenuItem($mainMenu[2], 'My Contacts', $myContactsActive);
			$this->assertMenuItem($mainMenu[3], 'Settings', $settingsActive);
		}
		
		private function assertMenuItem(MenuItem $menuItem, $label, $isActive) {
			$this->assertEqual($label, $menuItem->getLabel());
			$this->assertIdentical($isActive, $menuItem->isActive());
		}
	}
?>