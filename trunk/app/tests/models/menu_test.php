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
		
		function testGetFilterSubMenu() {
			$subMenu = $this->menu->getSubMenu(array('controller' => 'Identities', 'action' => 'social_stream'));
			$this->assertEqual(10, count($subMenu));
			$filterState = new FilterState();
			$this->assertFilterSubMenu($subMenu, '', $filterState);
		}
		
		function testGetFilterSubMenuWithOneItemSelected() {
			$filters = array('all', 'photo', 'video', 'audio', 'link', 'text', 'micropublish', 'event', 'document', 'location');
			
			foreach ($filters as $filter) {
				$subMenu = $this->menu->getSubMenu(array('controller' => 'Identities', 'action' => 'social_stream', 'filter' => $filter));
				$filterState = new FilterState($filter);
				$this->assertFilterSubMenu($subMenu, '', $filterState);
			}
		}
		
		function testGetSettingsSubMenu() {
			$subMenu = $this->menu->getSubMenu(array('local_username' => 'testuser'));
			$this->assertEqual(7, count($subMenu));
			$this->assertSettingsSubMenu($subMenu, 'testuser', false, false, false, false, false, false, false);
		}
		
		function testGetSettingsSubMenuWhenLoggedInWithOpenID() {
			$subMenu = $this->menu->getSubMenu(array('local_username' => 'testuser', 'openid_user' => true));
			$this->assertEqual(6, count($subMenu));
			$link = '/testuser/settings/';
			$this->assertMenuItem($subMenu[0], 'Profile', $link . 'profile/', false);
			$this->assertMenuItem($subMenu[1], 'Accounts', $link . 'accounts/', false);
			$this->assertMenuItem($subMenu[2], 'Privacy', $link . 'privacy/', false);
			$this->assertMenuItem($subMenu[3], 'Feeds', $link . 'feeds/', false);
			$this->assertMenuItem($subMenu[4], 'OpenID', $link . 'openid/', false);
			$this->assertMenuItem($subMenu[5], 'Delete account', $link . 'account/', false);
		}
		
		function testGetSettingsSubMenuWithOneItemSelected() {
			$controllers = array('Accounts', 'OpenidSites', 'Syndications');
			
			foreach ($controllers as $controller) {
				$subMenu = $this->menu->getSubMenu(array('local_username' => 'testuser', 'controller' => $controller));
				$this->assertSettingsSubMenu($subMenu, 'testuser', false, $controller == 'Accounts', false, $controller == 'Syndications', $controller == 'OpenidSites', false, false);
			}
			
			$identityActions = array('profile_settings', 'privacy_settings', 'password_settings', 'account_settings');
			
			foreach ($identityActions as $action) {
				$subMenu = $this->menu->getSubMenu(array('local_username' => 'testuser', 'controller' => 'Identities', 'action' => $action));
				$this->assertSettingsSubMenu($subMenu, 'testuser', $action == 'profile_settings', false, $action == 'privacy_settings', false, false, $action == 'password_settings', $action == 'account_settings');
			}
		}
		
		private function assertFilterSubMenu($subMenu, $urlPart, FilterState $filterState) {
			$this->assertMenuItem($subMenu[0], 'All', '', $filterState->isAllActive());
			$this->assertMenuItem($subMenu[1], 'Photo', '', $filterState->isPhotoActive());
			$this->assertMenuItem($subMenu[2], 'Video', '', $filterState->isVideoActive());
			$this->assertMenuItem($subMenu[3], 'Audio', '', $filterState->isAudioActive());
			$this->assertMenuItem($subMenu[4], 'Link', '', $filterState->isLinkActive());
			$this->assertMenuItem($subMenu[5], 'Text', '', $filterState->isTextActive());
			$this->assertMenuItem($subMenu[6], 'Micropublish', '', $filterState->isMicroPublishActive());
			$this->assertMenuItem($subMenu[7], 'Events', '', $filterState->isEventActive());
			$this->assertMenuItem($subMenu[8], 'Documents', '', $filterState->isDocumentActive());
			$this->assertMenuItem($subMenu[9], 'Locations', '', $filterState->isLocationActive());
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
		
		private function assertSettingsSubMenu($subMenu, $localUsername, $profileActive, $accountsActive, $privacyActive, $feedsActive, $openidActive, $passwordActive, $deleteAccountActive) {
			$link = '/' . $localUsername . '/settings/';
			$this->assertMenuItem($subMenu[0], 'Profile', $link . 'profile/', $profileActive);
			$this->assertMenuItem($subMenu[1], 'Accounts', $link . 'accounts/', $accountsActive);
			$this->assertMenuItem($subMenu[2], 'Privacy', $link . 'privacy/', $privacyActive);
			$this->assertMenuItem($subMenu[3], 'Feeds', $link . 'feeds/', $feedsActive);
			$this->assertMenuItem($subMenu[4], 'OpenID', $link . 'openid/', $openidActive);
			$this->assertMenuItem($subMenu[5], 'Password', $link . 'password/', $passwordActive);
			$this->assertMenuItem($subMenu[6], 'Delete account', $link . 'account/', $deleteAccountActive);
		}
	}
	
	// helper class
	class FilterState {
		private $activeItem = '';
		
		function __construct($activeItem = '') {
			$this->activeItem = $activeItem;
		}
		
		function isAllActive() {
			return ($this->activeItem == 'all');
		}
		
		function isPhotoActive() {
			return ($this->activeItem == 'photo');
		}
		
		function isVideoActive() {
			return ($this->activeItem == 'video');
		}
		
		function isAudioActive() {
			return ($this->activeItem == 'audio');
		}
		
		function isTextActive() {
			return ($this->activeItem == 'text');
		}
		
		function isLinkActive() {
			return ($this->activeItem == 'link');
		}
		
		function isMicroPublishActive() {
			return ($this->activeItem == 'micropublish');
		}
		
		function isEventActive() {
			return ($this->activeItem == 'event');
		}
		
		function isDocumentActive() {
			return ($this->activeItem == 'document');
		}
		
		function isLocationActive() {
			return ($this->activeItem == 'location');
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