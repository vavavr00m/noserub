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