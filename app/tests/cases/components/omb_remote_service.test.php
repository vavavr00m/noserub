<?php
App::import('Component', 'OmbRemoteService');
App::import('Vendor', array('OauthConstants', 'OmbConstants', 'OmbParamKeys'));

class OmbRemoteServiceComponentTest extends CakeTestCase {
	const IDENTICA = 'http://identi.ca';
	private $component = null;
	private $urlOfXRDS = null;
	
	public function setUp() {
		$this->component = new OmbRemoteServiceComponent();
		$this->urlOfXRDS = Configure::read('NoseRub.full_base_url') . 'testing/identica_0.6.xrds';
	}
	
	public function testDiscoverLocalService() {
		$this->assertLocalService($this->component->discoverLocalService($this->urlOfXRDS));
	}
	
	public function testDiscoverLocalServiceFromUrlWithoutHttp() {
		$urlOfXRDSWithoutHttp = str_replace('http://', '', $this->urlOfXRDS);
		$this->assertLocalService($this->component->discoverLocalService($urlOfXRDSWithoutHttp));
	}
	
	public function testDiscoverLocalServiceFromNonExistingUrl() {
		try {
			$this->component->discoverLocalService(Configure::read('NoseRub.full_base_url') . 'testing/notexisting');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}
	
	private function assertLocalService(OmbLocalServiceDefinition $localService) {
		$this->assertEqual(self::IDENTICA.'/user/4599', $localService->getLocalId());
		$this->assertEqual(self::IDENTICA.'/index.php?action=requesttoken', $localService->getRequestTokenUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=userauthorization', $localService->getAuthorizeUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=accesstoken', $localService->getAccessTokenUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=postnotice', $localService->getPostNoticeUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=updateprofile', $localService->getUpdateProfileUrl());
	}
}

class OmbAuthorizationResponseTest extends CakeTestCase {
	private $nickname = 'joe';
	private $profileUrl = 'http://example.com/joe';
	private $avatarUrl = 'http://example.com/joe/avatar.gif';
	private $requiredParams = null;
	
	public function setUp() {
		$this->requiredParams = $this->getRequiredParams();
	}
	
	public function testConstruct() {
		$response = new OmbAuthorizationResponse($this->getRequiredParams());
		$this->assertEqual($this->profileUrl, $response->getProfileUrl());
		$this->assertIdentical('', $response->getAvatarUrl());
	}
	
	public function testConstructWithAvatarUrl() {
		$data = array_merge($this->getRequiredParams(), array(OmbParamKeys::LISTENER_AVATAR => $this->avatarUrl));
		$response = new OmbAuthorizationResponse($data);
		$this->assertEqual($this->avatarUrl, $response->getAvatarUrl());
	}
	
	public function testConstructWithEmptyArray() {
		$this->assertInvalidArgumentException(array());
	}
	
	public function testConstructWithoutOmbVersion() {
		unset($this->requiredParams[OmbParamKeys::VERSION]);
		$this->assertInvalidArgumentException($this->requiredParams);
	}
	
	public function testConstructWithInvalidOmbVersion() {
		$this->requiredParams[OmbParamKeys::VERSION] = 'invalid_version';
		$this->assertInvalidArgumentException($this->requiredParams);			
	}
	
	public function testConstructWithoutListenerNickname() {
		unset($this->requiredParams[OmbParamKeys::LISTENER_NICKNAME]);
		$this->assertInvalidArgumentException($this->requiredParams);
	}
	
	public function testConstructWithEmptyListenerNickname() {
		$this->requiredParams[OmbParamKeys::LISTENER_NICKNAME] = '';
		$this->assertInvalidArgumentException($this->requiredParams);
	}
	
	public function testConstructWithoutListenerProfile() {
		unset($this->requiredParams[OmbParamKeys::LISTENER_PROFILE]);
		$this->assertInvalidArgumentException($this->requiredParams);
	}
	
	public function testConstructWithEmptyListenerProfile() {
		$this->requiredParams[OmbParamKeys::LISTENER_PROFILE] = '';
		$this->assertInvalidArgumentException($this->requiredParams);
	}
	
	private function assertInvalidArgumentException(array $params) {
		try {
			new OmbAuthorizationResponse($params);
			$this->fail('InvalidArgumentException expected');
		} catch (InvalidArgumentException $e) {
			$this->assertTrue(true);
		}
	}
	
	private function getRequiredParams() {
		return array(OmbParamKeys::VERSION => OmbConstants::VERSION,
					 OmbParamKeys::LISTENER_NICKNAME => $this->nickname,
					 OmbParamKeys::LISTENER_PROFILE => $this->profileUrl);
	}
}

class OmbUpdatedProfileDataTest extends CakeTestCase {
	private $firstname = 'Joe';
	private $lastname = 'Doe';
	private $bio = 'My bio';
	private $location = 'MyCity, MyCountry';
	private $avatar = 'myavatar';
	
	public function testConstructWithEmptyArray() {
		$data = new OmbUpdatedProfileData(array());
		$this->assertIdentical(array(), $data->getAsArray());
	}
	
	public function testConstructWithUpdatedFullname() {
		$fullname = $this->firstname . ' ' . $this->lastname;
		$profileData = $this->createProfileData();
		$this->assertEqual($fullname, $profileData[OmbParamKeys::LISTENEE_FULLNAME]);
	}
	
	public function testConstructWithUpdatedFullnameConsistingOfFirstname() {
		$this->lastname = '';
		$fullname = $this->firstname;
		$profileData = $this->createProfileData();
		$this->assertEqual($fullname, $profileData[OmbParamKeys::LISTENEE_FULLNAME]);
	}
	
	public function testConstructWithUpdatedFullnameConsistingOfLastname() {
		$this->firstname = '';
		$fullname = $this->lastname;
		$profileData = $this->createProfileData();
		$this->assertEqual($fullname, $profileData[OmbParamKeys::LISTENEE_FULLNAME]);
	}
	
	public function testConstructWithTooLongFullname() {
		$this->firstname = str_repeat('a', OmbListeneeFullname::MAX_LENGTH + 1);
		$this->lastname = '';
		$fullname = str_repeat('a', OmbListeneeFullname::MAX_LENGTH);
		$profileData = $this->createProfileData();
		$this->assertEqual($fullname, $profileData[OmbParamKeys::LISTENEE_FULLNAME]);
	}
	
	public function testConstructWithUpdatedAvatar() {
		$avatarUrl = Configure::read('NoseRub.full_base_url').'static/avatars/'.$this->avatar.'-medium.jpg';
		$profileData = $this->createProfileDataWithAvatar();
		$this->assertEqual($avatarUrl, $profileData[OmbParamKeys::LISTENEE_AVATAR]);
	}
	
	public function testConstructWithEmptyAvatar() {
		$this->avatar = '';
		$profileData = $this->createProfileDataWithAvatar();
		$this->assertEqual('', $profileData[OmbParamKeys::LISTENEE_AVATAR]);
	}
	
	public function testConstructWithGravatar() {
		$gravatar = 'http://gravatar.com/avatar/xy';
		$gravatar96x96 = $gravatar . '?s=96';
		$this->avatar = $gravatar;
		$profileData = $this->createProfileDataWithAvatar();
		$this->assertEqual($gravatar96x96, $profileData[OmbParamKeys::LISTENEE_AVATAR]);
	}
	
	public function testConstructWithUpdatedBio() {
		$profileData = $this->createProfileDataWithBio();
		$this->assertEqual($this->bio, $profileData[OmbParamKeys::LISTENEE_BIO]);
	}
	
	public function testConstructWithTooLongBio() {
		$this->bio = str_repeat('a', OmbListeneeBio::MAX_LENGTH + 1);
		$expectedBio = str_repeat('a', OmbListeneeBio::MAX_LENGTH);
		$profileData = $this->createProfileDataWithBio();
		$this->assertEqual($expectedBio, $profileData[OmbParamKeys::LISTENEE_BIO]);
	}
	
	public function testConstructWithUpdatedLocation() {
		$profileData = $this->createProfileDataWithLocation();
		$this->assertEqual($this->location, $profileData[OmbParamKeys::LISTENEE_LOCATION]);
	}
	
	public function testConstructWithTooLongLocation() {
		$this->location = str_repeat('a', OmbListeneeLocation::MAX_LENGTH + 1);
		$expectedLocation = str_repeat('a', OmbListeneeLocation::MAX_LENGTH);
		$profileData = $this->createProfileDataWithLocation();
		$this->assertEqual($expectedLocation, $profileData[OmbParamKeys::LISTENEE_LOCATION]);
	}
	
	private function createProfileData() {
		$profileData = new OmbUpdatedProfileData(array('Identity' => array('firstname' => $this->firstname, 
																		   'lastname' => $this->lastname)));
		
		return $profileData->getAsArray();
	}
	
	private function createProfileDataWithAvatar() {
		$profileData = new OmbUpdatedProfileData(array('Identity' => array('photo' => $this->avatar)));
		
		return $profileData->getAsArray();
	}
	
	private function createProfileDataWithBio() {
		$profileData = new OmbUpdatedProfileData(array('Identity' => array('about' => $this->bio)));
		
		return $profileData->getAsArray();
	}
	
	private function createProfileDataWithLocation() {
		$profileData = new OmbUpdatedProfileData(array('Identity' => array('address_shown' => $this->location)));
		
		return $profileData->getAsArray();
	}
}

class OmbListeneeBioTest extends CakeTestCase {
	public function testConstruct() {
		$bio = 'My bio';
		$listeneeBio = new OmbListeneeBio($bio);
		$this->assertEqual(OmbParamKeys::LISTENEE_BIO, $listeneeBio->getKey());
		$this->assertEqual($bio, $listeneeBio->getValue());
	}
	
	public function testConstructWithTooLongBio() {
		$bio = str_repeat('a', OmbListeneeBio::MAX_LENGTH + 1);
		$listeneeBio = new OmbListeneeBio($bio);
		$this->assertEqual(OmbListeneeBio::MAX_LENGTH, strlen($listeneeBio->getValue()));
	}
}

class OmbListeneeFullnameTest extends CakeTestCase {
	public function testConstruct() {
		$fullname = 'Firstname Lastname';
		$listeneeFullname = new OmbListeneeFullname($fullname);
		$this->assertEqual(OmbParamKeys::LISTENEE_FULLNAME, $listeneeFullname->getKey());
		$this->assertEqual($fullname, $listeneeFullname->getValue());
	}
	
	public function testConstructWithTooLongFullname() {
		$fullname = str_repeat('a', OmbListeneeFullname::MAX_LENGTH + 1);
		$listeneeFullname = new OmbListeneeFullname($fullname);
		$this->assertEqual(OmbListeneeFullname::MAX_LENGTH, strlen($listeneeFullname->getValue()));
	}
}

class OmbListeneeHomepageTest extends CakeTestCase {
	public function testConstruct() {
		$homepage = 'http://example.com';
		$listeneeHomepage = new OmbListeneeHomepage($homepage);
		$this->assertEqual(OmbParamKeys::LISTENEE_HOMEPAGE, $listeneeHomepage->getKey());
		$this->assertEqual($homepage, $listeneeHomepage->getValue());
	}
}

class OmbListeneeLicenseTest extends CakeTestCase {
	public function testConstruct() {
		$expectedLicense = OmbListeneeLicense::CREATIVE_COMMONS;
		$listeneeLicense = new OmbListeneeLicense();
		$this->assertEqual(OmbParamKeys::LISTENEE_LICENSE, $listeneeLicense->getKey());
		$this->assertEqual($expectedLicense, $listeneeLicense->getValue());
	}
}

class OmbListeneeLocationTest extends CakeTestCase {
	public function testConstruct() {
		$location = 'MyCity, MyCountry';
		$listeneeLocation = new OmbListeneeLocation($location);
		$this->assertEqual(OmbParamKeys::LISTENEE_LOCATION, $listeneeLocation->getKey());
		$this->assertEqual($location, $listeneeLocation->getValue());
	}
	
	public function testConstructWithTooLongLocation() {
		$location = str_repeat('a', OmbListeneeLocation::MAX_LENGTH + 1);
		$listeneeLocation = new OmbListeneeLocation($location);
		$this->assertEqual(OmbListeneeLocation::MAX_LENGTH, strlen($listeneeLocation->getValue()));
	}
}

class OmbListeneeNicknameTest extends CakeTestCase {
	public function testConstruct() {
		$nickname = 'nick';
		$listeneeNickname = new OmbListeneeNickname($nickname);
		$this->assertEqual(OmbParamKeys::LISTENEE_NICKNAME, $listeneeNickname->getKey());
		$this->assertEqual($nickname, $listeneeNickname->getValue());
	}
}

class OmbListeneeProfileTest extends CakeTestCase {
	public function testConstruct() {
		$profile = 'http://example.com/profile';
		$listeneeProfile = new OmbListeneeProfile($profile);
		$this->assertEqual(OmbParamKeys::LISTENEE_PROFILE, $listeneeProfile->getKey());
		$this->assertEqual($profile, $listeneeProfile->getValue());
	}
}

class OmbListenerTest extends CakeTestCase {
	public function testConstruct() {
		$listener = 'http://example.com/listener';
		$ombListener = new OmbListener($listener);
		$this->assertEqual(OmbParamKeys::LISTENER, $ombListener->getKey());
		$this->assertEqual($listener, $ombListener->getValue()); 
	}
}

class OmbVersionTest extends CakeTestCase {
	public function testConstruct() {
		$version = new OmbVersion();
		$this->assertEqual(OmbParamKeys::VERSION, $version->getKey());
		$this->assertEqual(OmbConstants::VERSION, $version->getValue());
	}
}