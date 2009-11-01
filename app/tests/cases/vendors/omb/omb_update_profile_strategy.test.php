<?php
App::import('File', 'MockRemoteService', array('file' => TESTS.'mocks'.DS.'mock_remote_service_component.php'));
App::import('Vendor', array('OmbUpdatedProfileData', 'OmbUpdateProfileStrategy'));

class OmbUpdateProfileStrategyTest extends CakeTestCase {
	private $remoteService = null;
	
	public function setUp() {
		$this->remoteService = new MockRemoteServiceComponent();
	}
	
	public function testStrategyWithNoAccessTokens() {
		$this->createStrategy(array())->execute();
		$this->assertEqual(0, $this->remoteService->getUpdateProfileCallCount());
	}
	
	public function testStrategyWithOneAccessToken() {
		$accessTokens = array($this->createAccessToken('http://example.com'));
		$this->createStrategy($accessTokens)->execute();
		$this->assertEqual(1, $this->remoteService->getUpdateProfileCallCount());
	}
	
	public function testStrategyWithTwoAccessTokens() {
		$accessTokens = array($this->createAccessToken('http://example.com'),
							  $this->createAccessToken('http://example.org'));
		$this->createStrategy($accessTokens)->execute();
		$this->assertEqual(2, $this->remoteService->getUpdateProfileCallCount());
	}
	
	public function testStrategyWithTwoAccessTokensWithTheSameUpdateProfileUrl() {
		$accessTokens = array($this->createAccessToken('http://example.com'),
							  $this->createAccessToken('http://example.com'));
		$this->createStrategy($accessTokens)->execute();
		$this->assertEqual(1, $this->remoteService->getUpdateProfileCallCount());
	}
	
	private function createAccessToken($updateProfileUrl) {
		return array('OmbLocalService' => array('update_profile_url' => $updateProfileUrl),
					 'OmbLocalServiceAccessToken' => array('token_key' => 'key', 'token_secret' => 'secret'));
	}
	
	private function createStrategy(array $accessTokens) {
		$profileData = new OmbUpdatedProfileData(array());
		return new OmbUpdateProfileStrategy($this->remoteService, $accessTokens, $profileData);
	}
}