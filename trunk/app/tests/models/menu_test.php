<?php

	class MenuTest extends CakeTestCase {
		private $menu = null;
		
		function setUp() {
			$this->menu = new Menu();
		}
		
		// local user
		
		function testGetMainMenuForLocalUser() {
			$mainMenu = $this->menu->getMainMenu(array('is_local' => true, 'local_username' => 'test'));
			$this->assertEqual(4, count($mainMenu));
			$this->assertMenuForLocalUser($mainMenu, 'test', false, false, false, false);
		}
		
		function testGetMainMenuWithMyContactsSelected() {
			$mainMenu = $this->menu->getMainMenu(array('is_local' => true, 'controller' => 'Contacts', 'local_username' => 'test'));
			$this->assertMenuForLocalUser($mainMenu, 'test', false, false, true, false);
		}
		
		function testGetMainMenuWithMyProfileSelected() {
			$mainMenu = $this->menu->getMainMenu(array('is_local' => true, 'controller' => 'Identities', 'action' => 'index', 'local_username' => 'test'));
			$this->assertMenuForLocalUser($mainMenu, 'test', false, true, false, false);
		}
		
		function testGetMainMenuWithSettingsSelected() {
			$controllers = array('Accounts', 'OpenidSites', 'Syndications');
			
			foreach ($controllers as $controller) {
				$mainMenu = $this->menu->getMainMenu(array('is_local' => true, 'controller' => $controller, 'local_username' => 'test'));
				$this->assertMenuForLocalUser($mainMenu, 'test', false, false, false, true);
			}
			
			$identityActions = array('account_settings', 'password_settings', 'privacy_settings', 'profile_settings');
			
			foreach ($identityActions as $action) {
				$mainMenu = $this->menu->getMainMenu(array('is_local' => true, 'controller' => 'Identities', 'action' => $action, 'local_username' => 'test'));
				$this->assertMenuForLocalUser($mainMenu, 'test', false, false, false, true);
			}
		}
		
		function testGetMainMenuWithSocialStreamSelected() {
			$mainMenu = $this->menu->getMainMenu(array('is_local' => true, 'controller' => 'Identities', 'action' => 'social_stream', 'local_username' => 'test'));
			$this->assertMenuForLocalUser($mainMenu, 'test', true, false, false, false);
		}		
		
		// remote user
		
		function testGetMainMenuForRemoteUser() {
			$mainMenu = $this->menu->getMainMenu(array('is_local' => false));
			$this->assertEqual(2, count($mainMenu));
			$this->assertMenuItem($mainMenu[0], 'Social Stream', '/social_stream/', false);
			$this->assertMenuItem($mainMenu[1], 'My Profile', '', false);
		}
		
		// anonymous user
		
		function testGetMainMenuForWebUsersWithRegistrationPossibility() {
			$mainMenu = $this->menu->getMainMenu(array('registration_type' => 'all'));
			$this->assertEqual(2, count($mainMenu));
			$this->assertMenuItem($mainMenu[0], 'Social Stream', '/social_stream/', false);
			$this->assertMenuItem($mainMenu[1], 'Add me!', '/pages/register/', false);
		}

		function testGetMainMenuWithRegisterSelected() {
			$mainMenu = $this->menu->getMainMenu(array('registration_type' => 'all', 'controller' => 'Identities', 'action' => 'register'));
			$this->assertMenuItem($mainMenu[0], 'Social Stream', '/social_stream/', false);
			$this->assertMenuItem($mainMenu[1], 'Add me!', '/pages/register/', true);
		}
		
		function testGetMainMenuForWebUsersWithoutRegistrationPossibility() {
			$mainMenu = $this->menu->getMainMenu(array('registration_type' => 'none'));
			$this->assertEqual(1, count($mainMenu));
			$this->assertMenuItem($mainMenu[0], 'Social Stream', '/social_stream/', false);
		}
		
		private function assertMenuForLocalUser($mainMenu, $localUsername, $socialStreamActive, $myProfileActive, $myContactsActive, $settingsActive) {
			$this->assertMenuItem($mainMenu[0], 'Social Stream', '/social_stream/', $socialStreamActive);
			$this->assertMenuItem($mainMenu[1], 'My Profile', '/' . $localUsername . '/', $myProfileActive);
			$this->assertMenuItem($mainMenu[2], 'My Contacts', '/' . $localUsername . '/contacts/', $myContactsActive);
			$this->assertMenuItem($mainMenu[3], 'Settings', '/' . $localUsername . '/settings/',$settingsActive);
		}
		
		private function assertMenuItem(MenuItem $menuItem, $label, $link, $isActive) {
			$this->assertEqual($label, $menuItem->getLabel());
			$this->assertEqual($link, $menuItem->getLink());
			$this->assertIdentical($isActive, $menuItem->isActive());
		}
	}
	
	class MenuitemTest extends CakeTestCase {
		function testMenuItem() {
			$label = 'A menu item';
			$link = '/test';
			$isActive = true;
			
			$menuItem = new MenuItem($label, $link, $isActive);
			$this->assertEqual($label, $menuItem->getLabel());
			$this->assertEqual($link, $menuItem->getLink());
			$this->assertIdentical($isActive, $menuItem->isActive());
		}
	}
	
	class MyContactsMenuItemTest extends CakeTestCase {
		function testCreateActivatedMyContactsMenuItem() {
			$menuItem = new MyContactsMenuItem('Contacts', 'testuser');
			$this->assertMenuItem($menuItem, '/testuser/contacts/', true);
		}
		
		function testCreateNotActivatedMyContactsMenuItem() {
			$menuItem = new MyContactsMenuItem('SomeController', 'test');
			$this->assertMenuItem($menuItem, '/test/contacts/', false);
		}
		
		private function assertMenuItem($menuItem, $link, $isActive) {
			$this->assertEqual('My Contacts', $menuItem->getLabel());
			$this->assertEqual($link, $menuItem->getLink());
			$this->assertIdentical($isActive, $menuItem->isActive());
		}
	}
	
	class MyProfileMenuItemTest extends CakeTestCase {
		function testCreateActivatedMyProfileMenuItem() {
			$menuItem = new MyProfileMenuItem('Identities', 'index', 'testuser');
			$this->assertMenuItem($menuItem, '/testuser/', true);
		}
		
		function testCreateNotActivatedMyProfileMenuItem() {
			$menuItem = new MyProfileMenuItem('SomeController', 'someaction', 'test');
			$this->assertMenuItem($menuItem, '/test/', false);
		}
		
		function testCreateMyProfileMenuItemWithoutUsername() {
			$menuItem = new MyProfileMenuItem('SomeController', 'someaction');
			$this->assertMenuItem($menuItem, '', false);
		}
		
		private function assertMenuItem($menuItem, $link, $isActive) {
			$this->assertEqual('My Profile', $menuItem->getLabel());
			$this->assertEqual($link, $menuItem->getLink());
			$this->assertIdentical($isActive, $menuItem->isActive());
		}
	}
	
	class RegisterMenuItemTest extends CakeTestCase {
		function testCreateActivatedRegisterMenuItem() {
			$menuItem = new RegisterMenuItem('Identities', 'register');
			$this->assertMenuItem($menuItem, true);
		}
		
		function testCreateNotActivatedRegisterMenuItem() {			
			$menuItem = new RegisterMenuItem('SomeController', 'someaction');
			$this->assertMenuItem($menuItem, false);
		}
		
		private function assertMenuItem($menuItem, $isActive) {
			$this->assertEqual('Add me!', $menuItem->getLabel());
			$this->assertEqual('/pages/register/', $menuItem->getLink());
			$this->assertIdentical($isActive, $menuItem->isActive());
		}
	}
	
	class SettingsMenuItemTest extends CakeTestCase {
		function testCreateActivatedSettingsMenuItem() {
			$controllers = array('Accounts', 'OpenidSites', 'Syndications');
			
			foreach ($controllers as $controller) {
				$menuItem = new SettingsMenuItem($controller, '', 'testuser');
				$this->assertMenuItem($menuItem, '/testuser/settings/', true);
			}
			
			$identityActions = array('account_settings', 'password_settings', 'privacy_settings', 'profile_settings');
			
			foreach ($identityActions as $action) {
				$menuItem = new SettingsMenuItem('Identities', $action, 'user');
				$this->assertMenuItem($menuItem, '/user/settings/', true);
			}
		}
		
		function testCreateNotActivatedSettingsMenuItem() {
			$menuItem = new SettingsMenuItem('SomeController', 'someaction', 'test');
			$this->assertMenuItem($menuItem, '/test/settings/', false);
		}
		
		private function assertMenuItem($menuItem, $link, $isActive) {
			$this->assertEqual('Settings', $menuItem->getLabel());
			$this->assertEqual($link, $menuItem->getLink());
			$this->assertIdentical($isActive, $menuItem->isActive());
		}
	}
	
	class SocialStreamMenuItemTest extends CakeTestCase {
		function testCreateActivatedSocialStreamMenuItem() {
			$menuItem = new SocialStreamMenuItem('Identities', 'social_stream');
			$this->assertMenuItem($menuItem, true);
		}
		
		function testCreateNotActivatedSocialStreamMenuItem() {			
			$menuItem = new SocialStreamMenuItem('SomeController', 'someaction');
			$this->assertMenuItem($menuItem, false);
		}
		
		private function assertMenuItem($menuItem, $isActive) {
			$this->assertEqual('Social Stream', $menuItem->getLabel());
			$this->assertEqual('/social_stream/', $menuItem->getLink());
			$this->assertIdentical($isActive, $menuItem->isActive());
		}
	}
?>