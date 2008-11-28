<?php
App::import('Component', 'OmbConsumer');
App::import('Vendor', 'OmbConstants');
App::import('Vendor', 'OauthConstants');

class OmbConsumerComponentTest extends CakeTestCase {
	const IDENTICA = 'http://identi.ca';
	private $component = null;
	
	public function setUp() {
		$this->component = new OmbConsumerComponent();
	}
	
	public function testDiscover() {
		$endPoint = $this->component->discover(Configure::read('NoseRub.full_base_url') . 'testing/identica_0.6.xrds');
		$this->assertEqual(self::IDENTICA.'/user/4599', $endPoint->getLocalId());
		$this->assertEqual(self::IDENTICA.'/index.php?action=requesttoken', $endPoint->getRequestTokenUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=userauthorization', $endPoint->getAuthorizeUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=accesstoken', $endPoint->getAccessTokenUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=postnotice', $endPoint->getPostNoticeUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=updateprofile', $endPoint->getUpdateProfileUrl());
	}
	
	public function testDiscoverNotExistingFile() {
		try {
			$this->component->discover(Configure::read('NoseRub.full_base_url') . 'testing/notexisting');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
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