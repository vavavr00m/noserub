<?php
App::import('Vendor', 'OmbLocalServiceDefinition');

class OmbLocalServiceDefinitionTest extends CakeTestCase {
	private $localId = 'http://example.com/user/12';
	private $requestTokenUrl = 'http://example.com/requestToken';
	private $authorizeUrl = 'http://example.com/authorize';
	private $accessTokenUrl = 'http://example.com/accessToken';
	private $postNoticeUrl = 'http://example.com/postNotice';
	private $updateProfileUrl = 'http://example.com/updateProfile';
	private $serviceDefinition = null;
	
	public function setUp() {
		$urls = array(OauthConstants::REQUEST => $this->requestTokenUrl,
					  OauthConstants::AUTHORIZE => $this->authorizeUrl,
					  OauthConstants::ACCESS => $this->accessTokenUrl,
					  OmbConstants::POST_NOTICE => $this->postNoticeUrl,
					  OmbConstants::UPDATE_PROFILE => $this->updateProfileUrl);
					  		
		$this->serviceDefinition = new OmbLocalServiceDefinition($this->localId, $urls);
	}
	
	public function testOmbLocalServiceDefinition() {
		$this->assertEqual($this->localId, $this->serviceDefinition->getLocalId());
		$this->assertEqual($this->requestTokenUrl, $this->serviceDefinition->getRequestTokenUrl());
		$this->assertEqual($this->authorizeUrl, $this->serviceDefinition->getAuthorizeUrl());
		$this->assertEqual($this->accessTokenUrl, $this->serviceDefinition->getAccessTokenUrl());
		$this->assertEqual($this->postNoticeUrl, $this->serviceDefinition->getPostNoticeUrl());
		$this->assertEqual($this->updateProfileUrl, $this->serviceDefinition->getUpdateProfileUrl());
	}
}