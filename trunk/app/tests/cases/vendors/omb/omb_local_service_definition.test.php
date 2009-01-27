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
	private $urls = null;
	
	public function setUp() {
		$this->urls = array(OauthConstants::REQUEST => $this->requestTokenUrl,
					  		OauthConstants::AUTHORIZE => $this->authorizeUrl,
					  		OauthConstants::ACCESS => $this->accessTokenUrl,
					  		OmbConstants::POST_NOTICE => $this->postNoticeUrl,
					  		OmbConstants::UPDATE_PROFILE => $this->updateProfileUrl);
					  		
		$this->serviceDefinition = new OmbLocalServiceDefinition($this->localId, $this->urls);
	}
	
	public function testOmbLocalServiceDefinition() {
		$this->assertEqual($this->localId, $this->serviceDefinition->getLocalId());
		$this->assertEqual($this->requestTokenUrl, $this->serviceDefinition->getRequestTokenUrl());
		$this->assertEqual($this->authorizeUrl, $this->serviceDefinition->getAuthorizeUrl());
		$this->assertEqual($this->accessTokenUrl, $this->serviceDefinition->getAccessTokenUrl());
		$this->assertEqual($this->postNoticeUrl, $this->serviceDefinition->getPostNoticeUrl());
		$this->assertEqual($this->updateProfileUrl, $this->serviceDefinition->getUpdateProfileUrl());
	}
	
	public function testOmbLocalServiceDefinitionWithMissingAccessTokenUrl() {
		unset($this->urls[OauthConstants::ACCESS]);
		$this->assertFailingConstructor();
	}
	
	public function testOmbLocalServiceDefinitionWithMissingAuthorizeUrl() {
		unset($this->urls[OauthConstants::AUTHORIZE]);
		$this->assertFailingConstructor();
	}
	
	public function testOmbLocalServiceDefinitionWithMissingPostNoticeUrl() {
		unset($this->urls[OmbConstants::POST_NOTICE]);
		$this->assertFailingConstructor();
	}
	
	public function testOmbLocalServiceDefinitionWithMissingRequestTokenUrl() {
		unset($this->urls[OauthConstants::REQUEST]);
		$this->assertFailingConstructor();
	}
	
	public function testOmbLocalServiceDefinitionWithMissingUpdateProfileUrl() {
		unset($this->urls[OmbConstants::UPDATE_PROFILE]);
		$this->assertFailingConstructor();
	}
	
	public function testOmbLocalServiceDefinitionWithInvalidAccessTokenUrl() {
		$this->urls[OauthConstants::ACCESS] = 'invalid';
		$this->assertFailingConstructor();
	}
	
	public function testOmbLocalServiceDefinitionWithInvalidAuthorizeUrl() {
		$this->urls[OauthConstants::AUTHORIZE] = 'invalid';
		$this->assertFailingConstructor();
	}
	
	public function testOmbLocalServiceDefinitionWithInvalidPostNoticeUrl() {
		$this->urls[OmbConstants::POST_NOTICE] = 'invalid';
		$this->assertFailingConstructor();
	}
	
	public function testOmbLocalServiceDefinitionWithInvalidRequestTokenUrl() {
		$this->urls[OauthConstants::REQUEST] = 'invalid';
		$this->assertFailingConstructor();
	}
	
	public function testOmbLocalServiceDefinitionWithInvalidUpdateProfileUrl() {
		$this->urls[OmbConstants::UPDATE_PROFILE] = 'invalid';
		$this->assertFailingConstructor();
	}
	
	private function assertFailingConstructor() {
		try {
			new OmbLocalServiceDefinition($this->localId, $this->urls);
			$this->fail('Exception expected');
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}
}