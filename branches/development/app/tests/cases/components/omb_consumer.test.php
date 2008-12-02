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
		$this->assertEndPoint($this->component->discover($this->urlOfXRDS));
	}
	
	public function testDiscoverFromUrlWithoutHttp() {
		$urlOfXRDSWithoutHttp = str_replace('http://', '', $this->urlOfXRDS);
		$this->assertEndPoint($this->component->discover($urlOfXRDSWithoutHttp));
	}
	
	public function testDiscoverNotExistingFile() {
		try {
			$this->component->discover(Configure::read('NoseRub.full_base_url') . 'testing/notexisting');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}
	
	private function assertEndPoint(OmbEndPoint $endPoint) {
		$this->assertEqual(self::IDENTICA.'/user/4599', $endPoint->getLocalId());
		$this->assertEqual(self::IDENTICA.'/index.php?action=requesttoken', $endPoint->getRequestTokenUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=userauthorization', $endPoint->getAuthorizeUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=accesstoken', $endPoint->getAccessTokenUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=postnotice', $endPoint->getPostNoticeUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=updateprofile', $endPoint->getUpdateProfileUrl());
	}
}

class OmbEndPointTest extends CakeTestCase {
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
	
	public function testOmbEndPoint() {
		$endPoint = new OmbEndPoint($this->localId, $this->urls);
		$this->assertEqual($this->localId, $endPoint->getLocalId());
		$this->assertEqual($this->requestTokenUrl, $endPoint->getRequestTokenUrl());
		$this->assertEqual($this->authorizeUrl, $endPoint->getAuthorizeUrl());
		$this->assertEqual($this->accessTokenUrl, $endPoint->getAccessTokenUrl());
		$this->assertEqual($this->postNoticeUrl, $endPoint->getPostNoticeUrl());
		$this->assertEqual($this->updateProfileUrl, $endPoint->getUpdateProfileUrl());
	}
}

class OmbParamsTest extends CakeTestCase {
	private $listener = 'http://example.com/user/12';
	private $nickname = 'joe';
	private $username = 'example.net/user/1';
	private $fullname = 'Joe Example';
	private $bio = 'My bio';
	private $location = 'MyCity, MyCountry';
	private $photo = 'joe_example';
	
	public function testGetAsArray() {
		$paramsArray = $this->getParamsArray();
		$this->assertEqual(OmbConstants::VERSION, $paramsArray[OmbParamKeys::VERSION]);
		$this->assertEqual($this->listener, $paramsArray[OmbParamKeys::LISTENER]);
		$this->assertEqual(Configure::read('NoseRub.full_base_url'), $paramsArray[OmbParamKeys::LISTENEE]);
		$this->assertEqual($this->nickname, $paramsArray[OmbParamKeys::LISTENEE_NICKNAME]);
		$this->assertEqual($this->getProfileUrl(), $paramsArray[OmbParamKeys::LISTENEE_PROFILE]);
		$this->assertEqual(OmbParams::CREATIVE_COMMONS_LICENSE, $paramsArray[OmbParamKeys::LISTENEE_LICENSE]);
		$this->assertEqual($this->getProfileUrl(), $paramsArray[OmbParamKeys::LISTENEE_HOMEPAGE]);
		$this->assertEqual($this->fullname, $paramsArray[OmbParamKeys::LISTENEE_FULLNAME]);
		$this->assertEqual($this->bio, $paramsArray[OmbParamKeys::LISTENEE_BIO]);
		$this->assertEqual($this->location, $paramsArray[OmbParamKeys::LISTENEE_LOCATION]);
		$this->assertEqual($this->getPhotoUrl(), $paramsArray[OmbParamKeys::LISTENEE_AVATAR]);
	}
	
	public function testGetAsArrayWithTooLongBio() {
		$this->bio = str_repeat('a', OmbParams::MAX_BIO_LENGTH + 1);
		$paramsArray = $this->getParamsArray();
		$this->assertEqual(OmbParams::MAX_BIO_LENGTH, strlen($paramsArray[OmbParamKeys::LISTENEE_BIO]));
	}
	
	public function testGetAsArrayWithTooLongFullname() {
		$this->fullname = str_repeat('a', OmbParams::MAX_FULLNAME_LENGTH + 1);
		$paramsArray = $this->getParamsArray();
		$this->assertEqual(OmbParams::MAX_FULLNAME_LENGTH, strlen($paramsArray[OmbParamKeys::LISTENEE_FULLNAME]));
	}
	
	public function testGetAsArrayWithTooLongLocation() {
		$this->location = str_repeat('a', OmbParams::MAX_LOCATION_LENGTH + 1);
		$paramsArray = $this->getParamsArray();
		$this->assertEqual(OmbParams::MAX_LOCATION_LENGTH, strlen($paramsArray[OmbParamKeys::LISTENEE_LOCATION]));
	}
	
	private function getListeneeData() {
		return array('Identity' => array('local_username' => $this->nickname,
										 'username' => $this->username,
										 'name' => $this->fullname,
										 'about' => $this->bio,
										 'address_shown' => $this->location,
										 'photo' => $this->photo));
	}
	
	private function getParamsArray() {
		$params = new OmbParams($this->listener, $this->getListeneeData());
		
		return $params->getAsArray();
	}
	
	private function getPhotoUrl() {
		return Configure::read('NoseRub.full_base_url').'static/avatars/'.$this->photo.'-medium.jpg';
	}
	
	private function getProfileUrl() {
		return 'http://'.$this->username;
	}
}