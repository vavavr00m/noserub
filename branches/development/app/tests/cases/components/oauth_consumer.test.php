<?php
App::import('Component', 'OauthConsumer');

class OauthConsumerComponentTest extends CakeTestCase {
	private $component = null;
	
	public function setUp() {
		$this->component = new MyOauthConsumerComponent();
	}
	
	public function testCreateOauthToken() {
		$token = 'theToken';
		$secret = 'theSecret';
		$result = $this->component->publicCreateOauthToken(array('oauth_token' => $token, 'oauth_token_secret' => $secret));
		$this->assertEqual($token, $result->key);
		$this->assertEqual($secret, $result->secret);
	}
	
	public function testCreateOauthTokenWithInvalidResponse() {
		$this->assertNull($this->component->publicCreateOauthToken(array()));
		$this->assertNull($this->component->publicCreateOauthToken(array('a' => 'b', 'c' => 'd')));		
	}
}

class MyOauthConsumerComponent extends OauthConsumerComponent {
	public function publicCreateOauthToken($response) {
		return $this->createOauthToken($response);
	}
}