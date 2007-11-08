<?php

	class MenuTest extends CakeTestCase {
		private $menu = null;
		
		function setUp() {
			$this->menu = new Menu();
		}
		
		function testGetMainMenuForLocalUser() {
			$mainMenu = $this->menu->getMainMenu(array('is_local' => true));
			$this->assertEqual(4, count($mainMenu));
			$this->assertMenuItem($mainMenu[0], 'Social Stream', false);
			$this->assertMenuItem($mainMenu[1], 'My Profile', false);
			$this->assertMenuItem($mainMenu[2], 'My Contacts', false);
			$this->assertMenuItem($mainMenu[3], 'Settings', false);
		}
		
		function testGetMainMenuForRemoteUser() {
			$mainMenu = $this->menu->getMainMenu(array('is_local' => false));
			$this->assertEqual(2, count($mainMenu));
			$this->assertMenuItem($mainMenu[0], 'Social Stream', false);
			$this->assertMenuItem($mainMenu[1], 'My Profile', false);
		}
		
		function testGetMainMenuForWebUsersWithoutRegistrationPossibility() {
			$mainMenu = $this->menu->getMainMenu(array('registration_type' => 'none'));
			$this->assertEqual(1, count($mainMenu));
			$this->assertMenuItem($mainMenu[0], 'Social Stream', false);
		}
		
		function testGetMainMenuForWebUsersWithRegistrationPossibility() {
			$mainMenu = $this->menu->getMainMenu(array('registration_type' => 'all'));
			$this->assertEqual(2, count($mainMenu));
			$this->assertMenuItem($mainMenu[0], 'Social Stream', false);
			$this->assertMenuItem($mainMenu[1], 'Add me!', false);
		}
		
		function testGetMainMenuWithSocialStreamSelected() {
			$mainMenu = $this->menu->getMainMenu(array('is_local' => true, 'controller' => 'Identities'));
			$this->assertMenuItem($mainMenu[0], 'Social Stream', true);
		}
		
		function testGetMainMenuWithSettingsSelected() {
			$controllers = array('Accounts', 'OpenidSites', 'Syndications');
			
			foreach ($controllers as $controller) {
				$mainMenu = $this->menu->getMainMenu(array('is_local' => true, 'controller' => $controller));
				$this->assertMenuItem($mainMenu[3], 'Settings', true);
			}
		}
		
		private function assertMenuItem(MenuItem $menuItem, $label, $isActive) {
			$this->assertEqual($label, $menuItem->getLabel());
			$this->assertIdentical($isActive, $menuItem->isActive());
		}
	}
?>