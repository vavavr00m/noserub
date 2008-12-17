<?php
App::import('Component', 'OmbConsumer');
App::import('Vendor', array('OauthConstants', 'OmbConstants', 'OmbParamKeys'));

class OmbConsumerComponentTest extends CakeTestCase {
	const IDENTICA = 'http://identi.ca';
	private $component = null;
	private $urlOfXRDS = null;
	
	public function setUp() {
		$this->component = new OmbConsumerComponent();
		$this->urlOfXRDS = Configure::read('NoseRub.full_base_url') . 'testing/identica_0.6.xrds';
	}
	
	public function testDiscover() {
		$this->assertLocalService($this->component->discover($this->urlOfXRDS));
	}
	
	public function testDiscoverFromUrlWithoutHttp() {
		$urlOfXRDSWithoutHttp = str_replace('http://', '', $this->urlOfXRDS);
		$this->assertLocalService($this->component->discover($urlOfXRDSWithoutHttp));
	}
	
	public function testDiscoverNotExistingFile() {
		try {
			$this->component->discover(Configure::read('NoseRub.full_base_url') . 'testing/notexisting');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}
	
	private function assertLocalService(OmbDiscoveredLocalService  $localService) {
		$this->assertEqual(self::IDENTICA.'/user/4599', $localService->getLocalId());
		$this->assertEqual(self::IDENTICA.'/index.php?action=requesttoken', $localService->getRequestTokenUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=userauthorization', $localService->getAuthorizeUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=accesstoken', $localService->getAccessTokenUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=postnotice', $localService->getPostNoticeUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=updateprofile', $localService->getUpdateProfileUrl());
	}
}

class OmbDiscoveredLocalServiceTest extends CakeTestCase {
	private $localId = 'http://example.com/user/12';
	private $requestTokenUrl = 'http://example.com/requestToken';
	private $authorizeUrl = 'http://example.com/authorize';
	private $accessTokenUrl = 'http://example.com/accessToken';
	private $postNoticeUrl = 'http://example.com/postNotice';
	private $updateProfileUrl = 'http://example.com/updateProfile';
	private $urls = null;
	
	public function setUp() {
		$this->urls = array(OauthConstants::REQUEST => $this->requestTokenUrl,
					  		OauthConstants::AUTHORIZE => $this->authorizeUrl,
					  		OauthConstants::ACCESS => $this->accessTokenUrl,
					  		OmbConstants::POST_NOTICE => $this->postNoticeUrl,
					  		OmbConstants::UPDATE_PROFILE => $this->updateProfileUrl);
	}
	
	public function testOmbDiscoveredLocalService() {
		$localService = new OmbDiscoveredLocalService($this->localId, $this->urls);
		$this->assertEqual($this->localId, $localService->getLocalId());
		$this->assertEqual($this->requestTokenUrl, $localService->getRequestTokenUrl());
		$this->assertEqual($this->authorizeUrl, $localService->getAuthorizeUrl());
		$this->assertEqual($this->accessTokenUrl, $localService->getAccessTokenUrl());
		$this->assertEqual($this->postNoticeUrl, $localService->getPostNoticeUrl());
		$this->assertEqual($this->updateProfileUrl, $localService->getUpdateProfileUrl());
	}
}

class OmbAuthorizationParamsTest extends CakeTestCase {
	private $listener = 'http://example.com/user/12';
	private $nickname = 'joe';
	private $username = 'example.net/user/1';
	private $fullname = 'Joe Example';
	private $bio = 'My bio';
	private $location = 'MyCity, MyCountry';
	private $photo = 'joe_example';
	
	public function testGetAsArray() {
		$paramsArray = $this->getAuthorizationParamsArray();
		$this->assertEqual(OmbConstants::VERSION, $paramsArray[OmbParamKeys::VERSION]);
		$this->assertEqual($this->listener, $paramsArray[OmbParamKeys::LISTENER]);
		$this->assertEqual($this->getProfileUrl(), $paramsArray[OmbParamKeys::LISTENEE]);
		$this->assertEqual($this->nickname, $paramsArray[OmbParamKeys::LISTENEE_NICKNAME]);
		$this->assertEqual($this->getProfileUrl(), $paramsArray[OmbParamKeys::LISTENEE_PROFILE]);
		$this->assertEqual(OmbAuthorizationParams::CREATIVE_COMMONS_LICENSE, $paramsArray[OmbParamKeys::LISTENEE_LICENSE]);
		$this->assertEqual($this->getProfileUrl(), $paramsArray[OmbParamKeys::LISTENEE_HOMEPAGE]);
		$this->assertEqual($this->fullname, $paramsArray[OmbParamKeys::LISTENEE_FULLNAME]);
		$this->assertEqual($this->bio, $paramsArray[OmbParamKeys::LISTENEE_BIO]);
		$this->assertEqual($this->location, $paramsArray[OmbParamKeys::LISTENEE_LOCATION]);
		$this->assertEqual($this->getPhotoUrl(), $paramsArray[OmbParamKeys::LISTENEE_AVATAR]);
	}
	
	public function testGetAsArrayWithTooLongBio() {
		$this->bio = str_repeat('a', OmbListeneeBio::MAX_LENGTH + 1);
		$paramsArray = $this->getAuthorizationParamsArray();
		$this->assertEqual(OmbListeneeBio::MAX_LENGTH, strlen($paramsArray[OmbParamKeys::LISTENEE_BIO]));
	}
	
	public function testGetAsArrayWithTooLongFullname() {
		$this->fullname = str_repeat('a', OmbListeneeFullname::MAX_LENGTH + 1);
		$paramsArray = $this->getAuthorizationParamsArray();
		$this->assertEqual(OmbListeneeFullname::MAX_LENGTH, strlen($paramsArray[OmbParamKeys::LISTENEE_FULLNAME]));
	}
	
	public function testGetAsArrayWithTooLongLocation() {
		$this->location = str_repeat('a', OmbListeneeLocation::MAX_LENGTH + 1);
		$paramsArray = $this->getAuthorizationParamsArray();
		$this->assertEqual(OmbListeneeLocation::MAX_LENGTH, strlen($paramsArray[OmbParamKeys::LISTENEE_LOCATION]));
	}
	
	public function testGetAsArrayWithoutPhoto() {
		$this->photo = '';
		$paramsArray = $this->getAuthorizationParamsArray();
		$this->assertEqual('', $paramsArray[OmbParamKeys::LISTENEE_AVATAR]);
	}
	
	public function testGetAsArrayWithGravatarAsPhoto() {
		$gravatar = 'http://gravatar.com/avatar/xy';
		$gravatar96x96 = $gravatar . '?s=96';
		$this->photo = $gravatar;
		$paramsArray = $this->getAuthorizationParamsArray();
		$this->assertEqual($gravatar96x96, $paramsArray[OmbParamKeys::LISTENEE_AVATAR]);
	}
	
	private function getAuthorizationParamsArray() {
		$params = new OmbAuthorizationParams($this->listener, $this->getListeneeData());
		
		return $params->getAsArray();
	}
	
	private function getListeneeData() {
		return array('Identity' => array('local_username' => $this->nickname,
										 'username' => $this->username,
										 'name' => $this->fullname,
										 'about' => $this->bio,
										 'address_shown' => $this->location,
										 'photo' => $this->photo));
	}
	
	private function getPhotoUrl() {
		return Configure::read('NoseRub.full_base_url').'static/avatars/'.$this->photo.'-medium.jpg';
	}
	
	private function getProfileUrl() {
		return 'http://'.$this->username;
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

class OmbMaxLengthEnforcerTest extends CakeTestCase {
	public function testEnsureBioLength() {
		$bio = str_repeat('a', OmbListeneeBio::MAX_LENGTH);
		$tooLongBio = $bio . 'a';
		$this->assertEqual($bio, OmbMaxLengthEnforcer::ensureBioLength($bio));
		$this->assertEqual($bio, OmbMaxLengthEnforcer::ensureBioLength($tooLongBio));
	}
	
	public function testEnsureFullnameLength() {
		$fullname = str_repeat('a', OmbListeneeFullname::MAX_LENGTH);
		$tooLongFullname = $fullname . 'a';
		$this->assertEqual($fullname, OmbMaxLengthEnforcer::ensureFullnameLength($fullname));
		$this->assertEqual($fullname, OmbMaxLengthEnforcer::ensureFullnameLength($tooLongFullname));
	}
	
	public function testEnsureLocationLength() {
		$location = str_repeat('a', OmbListeneeLocation::MAX_LENGTH);
		$tooLongLocation = $location . 'a';
		$this->assertEqual($location, OmbMaxLengthEnforcer::ensureLocationLength($location));
		$this->assertEqual($location, OmbMaxLengthEnforcer::ensureLocationLength($tooLongLocation));
	}
}

class OmbListeneeAvatarTest extends CakeTestCase {
	public function testConstruct() {
		$avatarName = 'myavatar';
		$avatarUrl = Configure::read('NoseRub.full_base_url').'static/avatars/'.$avatarName.'-medium.jpg';
		$listeneeAvatar = new OmbListeneeAvatar($avatarName);
		$this->assertEqual(OmbParamKeys::LISTENEE_AVATAR, $listeneeAvatar->getKey());
		$this->assertEqual($avatarUrl, $listeneeAvatar->getValue());
	}
	
	public function testConstructWithEmptyAvatar() {
		$avatarName = '';
		$listeneeAvatar = new OmbListeneeAvatar($avatarName);
		$this->assertEqual($avatarName, $listeneeAvatar->getValue());
	}
	
	public function testConstructWithGravatar() {
		$gravatar = 'http://gravatar.com/avatar/xy';
		$gravatar96x96 = $gravatar . '?s=96';
		$listeneeAvatar = new OmbListeneeAvatar($gravatar);
		$this->assertEqual($gravatar96x96, $listeneeAvatar->getValue());
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