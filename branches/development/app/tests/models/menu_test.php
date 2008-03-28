<?php

class MenuTest extends CakeTestCase {
	function testMenu() {
		$menuItems[] = new MenuItem('A', '', false);
		$menuItems[] = new MenuItem('B', '', false);
		$menuItems[] = new MenuItem('C', '', false);
		$menu = new Menu($menuItems);
		$menuItems = $menu->getMenuItems();
		$this->assertEqual(3, count($menuItems));
		$this->assertEqual('A', $menuItems[0]->getLabel());
		$this->assertEqual('B', $menuItems[1]->getLabel());
		$this->assertEqual('C', $menuItems[2]->getLabel());
		$this->assertIdentical(false, $menu->getActiveMenuItem());
	}
	
	function testGetActiveMenuItem() {
		$menuItems[] = new MenuItem('A', '', false);
		$menuItems[] = new MenuItem('B', '', true);
		$menuItems[] = new MenuItem('C', '', false);
		$menu = new Menu($menuItems);
		$activeMenuItem = $menu->getActiveMenuItem();
		$this->assertEqual('B', $activeMenuItem->getLabel());
	}
}

class MenuFactoryTest extends CakeTestCase {
	private $factory = null;
	
	function setUp() {
		$this->factory = new MenuFactory();
	}
	
	// local user
	
	function testGetMainMenuForLocalUser() {
		$mainMenu = $this->factory->getMainMenu(array('is_local' => true, 'local_username' => 'test'));
		$this->assertEqual(4, count($mainMenu->getMenuItems()));
		$this->assertMenuForLocalUser($mainMenu, 'test', false, false, false, false);
	}
	
	function testGetMainMenuWithMyContactsSelected() {
		$mainMenu = $this->factory->getMainMenu(array('is_local' => true, 'controller' => 'Contacts', 'action' => 'index', 'local_username' => 'test'));
		$this->assertMenuForLocalUser($mainMenu, 'test', false, false, true, false);
	}
	
	function testGetMainMenuWithMyProfileSelected() {
		$mainMenu = $this->factory->getMainMenu(array('is_local' => true, 'controller' => 'Identities', 'action' => 'index', 'local_username' => 'test'));
		$this->assertMenuForLocalUser($mainMenu, 'test', false, true, false, false);
	}
	
	function testGetMainMenuWithSettingsSelected() {
		$controllers = array('Accounts', 'OpenidSites', 'Syndications');
		
		foreach ($controllers as $controller) {
			$mainMenu = $this->factory->getMainMenu(array('is_local' => true, 'controller' => $controller, 'local_username' => 'test'));
			$this->assertMenuForLocalUser($mainMenu, 'test', false, false, false, true);
		}
		
		$identityActions = array('account_settings', 'password_settings', 'privacy_settings', 'profile_settings');
		
		foreach ($identityActions as $action) {
			$mainMenu = $this->factory->getMainMenu(array('is_local' => true, 'controller' => 'Identities', 'action' => $action, 'local_username' => 'test'));
			$this->assertMenuForLocalUser($mainMenu, 'test', false, false, false, true);
		}
	}
	
	function testGetMainMenuWithSocialStreamSelected() {
		$mainMenu = $this->factory->getMainMenu(array('is_local' => true, 'controller' => 'Identities', 'action' => 'social_stream', 'local_username' => 'test'));
		$this->assertMenuForLocalUser($mainMenu, 'test', true, false, false, false);
	}		
	
	// remote user
	
	function testGetMainMenuForRemoteUser() {
		$mainMenu = $this->factory->getMainMenu(array('is_local' => false));
		$menuItems = $mainMenu->getMenuItems();
		$this->assertEqual(2, count($menuItems));
		$this->assertMenuItem($menuItems[0], 'Social Stream', '/social_stream/', false);
		$this->assertMenuItem($menuItems[1], 'My Profile', '', false);
	}
	
	// anonymous user
	
	function testGetMainMenuForWebUsersWithRegistrationPossibility() {
		$mainMenu = $this->factory->getMainMenu(array('registration_type' => 'all'));
		$this->assertEqual(2, count($mainMenu->getMenuItems()));
		$this->assertMenuForAnonymousUser($mainMenu, false, false);
	}

	function testGetMainMenuWithRegisterSelected() {
		$registerActions = array('register', 'register_with_openid_step_1', 'register_with_openid_step_2');
		
		foreach ($registerActions as $action) {
			$mainMenu = $this->factory->getMainMenu(array('registration_type' => 'all', 'controller' => 'Identities', 'action' => $action));
			$this->assertMenuForAnonymousUser($mainMenu, false, true);
		}
	}
	
	function testGetMainMenuForWebUsersWithoutRegistrationPossibility() {
		$mainMenu = $this->factory->getMainMenu(array('registration_type' => 'none'));
		$menuItems = $mainMenu->getMenuItems();
		$this->assertEqual(1, count($menuItems));
		$this->assertMenuItem($menuItems[0], 'Social Stream', '/social_stream/', false);
	}
	
	// sub menus
	
	function testGetNoSubMenuOnRegisterPage() {
		$subMenu = $this->factory->getSubMenu(array('controller' => 'Identities', 'action' => 'register'));
		$this->assertIdentical(false, $subMenu);
	}
	
	function testGetFilterSubMenu() {
		$subMenu = $this->factory->getSubMenu(array('controller' => 'Identities', 'action' => 'social_stream'));
		$this->assertEqual(10, count($subMenu->getMenuItems()));
		$filterState = new FilterState();
		$this->assertFilterSubMenu($subMenu, '/social_stream/', $filterState);
	}
	
	function testGetFilterSubMenuForMyProfile() {
		$subMenu = $this->factory->getSubMenu(array('controller' => 'Identities', 'action' => 'index', 'local_username' => 'testuser'));
		$filterState = new FilterState();
		$this->assertFilterSubMenu($subMenu, '/testuser/', $filterState);
	}
	
	function testGetFilterSubMenuForContactsNetwork() {
		$subMenu = $this->factory->getSubMenu(array('controller' => 'Contacts', 'action' => 'network', 'local_username' => 'testuser'));
		$filterState = new FilterState();
		$this->assertFilterSubMenu($subMenu, '/testuser/network/', $filterState);
	}
	
	function testGetFilterSubMenuWithOneItemSelected() {
		$filters = array('all', 'photo', 'video', 'audio', 'link', 'text', 'micropublish', 'event', 'document', 'location');
		
		foreach ($filters as $filter) {
			$subMenu = $this->factory->getSubMenu(array('controller' => 'Identities', 'action' => 'social_stream', 'filter' => $filter));
			$filterState = new FilterState($filter);
			$this->assertFilterSubMenu($subMenu, '/social_stream/', $filterState);
		}
	}
	
	function testGetSettingsSubMenu() {
		$subMenu = $this->factory->getSubMenu(array('controller' => 'Identities', 'action' => 'privacy_settings', 'local_username' => 'testuser'));
		$this->assertEqual(10, count($subMenu->getMenuItems()));
		$settingState = new SettingState('Privacy');
		$this->assertSettingsSubMenu($subMenu, false, 'testuser', $settingState);
	}
	
	function testGetSettingsSubMenuWhenLoggedInWithOpenID() {
		$subMenu = $this->factory->getSubMenu(array('controller' => 'Identities', 'action' => 'privacy_settings', 'local_username' => 'testuser', 'openid_user' => true));
		$this->assertEqual(9, count($subMenu->getMenuItems()));
		$settingState = new SettingState('Privacy');
		$this->assertSettingsSubMenu($subMenu, true, 'testuser', $settingState);		
	}
	
	function testGetSettingsSubMenuWithOneItemSelected() {
		$controllers = array('Accounts', 'OpenidSites', 'Syndications');
		
		foreach ($controllers as $controller) {
			$subMenu = $this->factory->getSubMenu(array('local_username' => 'testuser', 'controller' => $controller));
			
			$activeItem = '';
			switch ($controller) {
				case 'Accounts':     $activeItem = 'Accounts';
								     break;
				case 'Syndications': $activeItem = 'Feeds';
									 break;
				case 'OpenidSites':  $activeItem = 'OpenID';
									 break;
			}
			
			$settingState = new SettingState($activeItem);
			$this->assertSettingsSubMenu($subMenu, false, 'testuser', $settingState);
		}
		
		$identityActions = array('profile_settings', 'privacy_settings', 'password_settings', 'account_settings');
		
		foreach ($identityActions as $action) {
			$subMenu = $this->factory->getSubMenu(array('local_username' => 'testuser', 'controller' => 'Identities', 'action' => $action));
			
			$activeItem = '';
			switch ($action) {
				case 'profile_settings':  $activeItem = 'Profile';
										  break;
				case 'privacy_settings':  $activeItem = 'Privacy';
										  break;
				case 'password_settings': $activeItem = 'Password';
										  break;
				case 'account_settings':  $activeItem = 'Manage';
										  break;
			}
			
			$settingState = new SettingState($activeItem);
			$this->assertSettingsSubMenu($subMenu, false, 'testuser', $settingState);
		}
	}
	
	private function assertFilterSubMenu(Menu $subMenu, $urlPart, FilterState $filterState) {
		$menuItems = $subMenu->getMenuItems();
		$this->assertMenuItem($menuItems[0], 'Overview', $urlPart, $filterState->isAllActive());
		$this->assertMenuItem($menuItems[1], 'Photo', $urlPart.'photo/', $filterState->isPhotoActive());
		$this->assertMenuItem($menuItems[2], 'Video', $urlPart.'video/', $filterState->isVideoActive());
		$this->assertMenuItem($menuItems[3], 'Audio', $urlPart.'audio/', $filterState->isAudioActive());
		$this->assertMenuItem($menuItems[4], 'Link', $urlPart.'link/', $filterState->isLinkActive());
		$this->assertMenuItem($menuItems[5], 'Text', $urlPart.'text/', $filterState->isTextActive());
		$this->assertMenuItem($menuItems[6], 'Micropublish', $urlPart.'micropublish/', $filterState->isMicroPublishActive());
		$this->assertMenuItem($menuItems[7], 'Events', $urlPart.'event/', $filterState->isEventActive());
		$this->assertMenuItem($menuItems[8], 'Documents', $urlPart.'document/', $filterState->isDocumentActive());
		$this->assertMenuItem($menuItems[9], 'Locations', $urlPart.'location/', $filterState->isLocationActive());
	}
	
	private function assertMenuForAnonymousUser(Menu $mainMenu, $socialStreamActive, $registerActive) {
		$menuItems = $mainMenu->getMenuItems();
		$this->assertMenuItem($menuItems[0], 'Social Stream', '/social_stream/', $socialStreamActive);
		$this->assertMenuItem($menuItems[1], 'Add me!', '/pages/register/', $registerActive);
	}
	
	private function assertMenuForLocalUser(Menu $mainMenu, $localUsername, $socialStreamActive, $myProfileActive, $myContactsActive, $settingsActive) {
		$menuItems = $mainMenu->getMenuItems();
		$this->assertMenuItem($menuItems[0], 'Social Stream', '/social_stream/', $socialStreamActive);
		$this->assertMenuItem($menuItems[1], 'My Profile', '/' . $localUsername . '/', $myProfileActive);
		$this->assertMenuItem($menuItems[2], 'My Contacts', '/' . $localUsername . '/network/', $myContactsActive);
		$this->assertMenuItem($menuItems[3], 'Settings', '/' . $localUsername . '/settings/',$settingsActive);
	}
	
	private function assertMenuItem(MenuItem $menuItem, $label, $link, $isActive) {
		$this->assertEqual($label, $menuItem->getLabel());
		$this->assertEqual($link, $menuItem->getLink());
		$this->assertIdentical($isActive, $menuItem->isActive());
	}
	
	private function assertSettingsSubMenu(Menu $subMenu, $isMenuForOpenIDUser, $localUsername, SettingState $settingState) {
		$menuItems = $subMenu->getMenuItems();
		$link = '/' . $localUsername . '/settings/';
		$i = 0;
		$this->assertMenuItem($menuItems[$i++], 'Profile', $link . 'profile/', $settingState->isProfileActive());
		$this->assertMenuItem($menuItems[$i++], 'Accounts', $link . 'accounts/', $settingState->isAccountsActive());
		$this->assertMenuItem($menuItems[$i++], 'Locations', $link . 'locations/', $settingState->isLocationsActive());
		$this->assertMenuItem($menuItems[$i++], 'Display', $link . 'display/', $settingState->isDisplayActive());
		$this->assertMenuItem($menuItems[$i++], 'Privacy', $link . 'privacy/', $settingState->isPrivacyActive());
		$this->assertMenuItem($menuItems[$i++], 'Feeds', $link . 'feeds/', $settingState->isFeedsActive());
		$this->assertMenuItem($menuItems[$i++], 'OpenID', $link . 'openid/', $settingState->isOpenIDActive());
		if (!$isMenuForOpenIDUser) {
			$this->assertMenuItem($menuItems[$i++], 'Password', $link . 'password/', $settingState->isPasswordActive());
		}
		$this->assertMenuItem($menuItems[$i++], 'Manage', $link . 'account/', $settingState->isManageActive());
		$this->assertMenuItem($menuItems[$i++], 'OAuth', $link . 'oauth/', $settingState->isOAuthActive());
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

// helper class
class SettingState {
	private $activeItem = '';
	
	public function __construct($activeItem = '') {
		$this->activeItem = $activeItem;
	}
	
	public function isProfileActive() {
		return ($this->activeItem == 'Profile');
	}
	
	public function isAccountsActive() {
		return ($this->activeItem == 'Accounts');
	}
	
	public function isLocationsActive() {
		return ($this->activeItem == 'Locations');
	}
	
	public function isDisplayActive() {
		return ($this->activeItem == 'Display');
	}
	
	public function isPrivacyActive() {
		return ($this->activeItem == 'Privacy');
	}
	
	public function isFeedsActive() {
		return ($this->activeItem == 'Feeds');
	}
	
	public function isOpenIDActive() {
		return ($this->activeItem == 'OpenID');
	}
	
	public function isPasswordActive() {
		return ($this->activeItem == 'Password');
	}
	
	public function isManageActive() {
		return ($this->activeItem == 'Manage');
	}
	
	public function isOAuthActive() {
		return ($this->activeItem == 'OAuth');
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
		$username = 'testuser';
		$menuItem = new MyContactsMenuItem('Contacts', 'index', $username);
		$this->assertMenuItem($menuItem, $username, true);

		$menuItem = new MyContactsMenuItem('Contacts', 'network', $username);
		$this->assertMenuItem($menuItem, $username, true);
	}
	
	function testCreateNotActivatedMyContactsMenuItem() {			
		$username = 'test';
		$menuItem = new MyContactsMenuItem('SomeController', 'someaction', $username);
		$this->assertMenuItem($menuItem, $username, false);
	}
	
	private function assertMenuItem($menuItem, $username, $isActive) {
		$this->assertEqual('My Contacts', $menuItem->getLabel());
		$this->assertEqual('/'.$username.'/network/', $menuItem->getLink());
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
		$registerActions = array('register', 'register_with_openid_step_1', 'register_with_openid_step_2');
		
		foreach ($registerActions as $action) {
			$menuItem = new RegisterMenuItem('Identities', $action);
			$this->assertMenuItem($menuItem, true);
		}
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