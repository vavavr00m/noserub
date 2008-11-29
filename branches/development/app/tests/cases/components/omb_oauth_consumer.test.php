<?php
App::import('Component', 'OmbOauthConsumer');

class OmbOauthConsumerComponentTest extends CakeTestCase {
	private $component = null;
	private $token = 'theToken';
	private $secret = 'theSecret';
	
	public function setUp() {
		$this->component = new MyOmbOauthConsumerComponent();
	}
	
	public function testCreateOauthToken() {
		$result = $this->component->publicCreateOauthToken(array('oauth_token' => $this->token, 
																 'oauth_token_secret' => $this->secret,
																 'omb_version' => OmbConstants::VERSION));
		$this->assertEqual($this->token, $result->key);
		$this->assertEqual($this->secret, $result->secret);
	}
	
	// XXX due to bug http://laconi.ca/trac/ticket/681 we have to allow tokens without omb_version
	public function testCreateOauthTokenWithMissingOmbVersion() {
		$result = $this->component->publicCreateOauthToken(array('oauth_token' => $this->token, 
																 'oauth_token_secret' => $this->secret));
		$this->assertEqual($this->token, $result->key);
		$this->assertEqual($this->secret, $result->secret);
	}
	
	public function testCreateOauthTokenWithInvalidOmbVersion() {
		try {
			$this->component->publicCreateOauthToken(array('oauth_token' => $this->token, 
														   'oauth_token_secret' => $this->secret,
														   'omb_version' => 'invalid.version.1.0'));
			$this->fail('InvalidArgumentException expected');
		} catch (InvalidArgumentException $e) {
			$this->assertTrue(true);
		}
	}
}

class MyOmbOauthConsumerComponent extends OmbOauthConsumerComponent {
	public function publicCreateOauthToken($response) {
		return $this->createOauthToken($response);
	}
}