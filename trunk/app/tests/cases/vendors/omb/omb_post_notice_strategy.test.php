<?php
App::import('File', 'MockRemoteService', array('file' => TESTS.'mocks'.DS.'mock_remote_service_component.php'));
App::import('Vendor', array('OmbNotice', 'OmbPostNoticeStrategy'));

class OmbPostNoticeStrategyTest extends CakeTestCase {
	private $remoteService = null;
	
	public function setUp() {
		$this->remoteService = new MockRemoteServiceComponent();
	}
	
	public function testStrategyWithNoAccessTokens() {
		$this->createStrategy(array())->execute();
		$this->assertEqual(0, $this->remoteService->getPostNoticeCallCount());
	}
	
	public function testStrategyWithOneAccessToken() {
		$accessTokens = array($this->createAccessToken('http://example.com'));
		$this->createStrategy($accessTokens)->execute();
		$this->assertEqual(1, $this->remoteService->getPostNoticeCallCount());
	}
	
	public function testStrategyWithTwoAccessTokens() {
		$accessTokens = array($this->createAccessToken('http://example.com'),
							  $this->createAccessToken('http://example.org'));
		$this->createStrategy($accessTokens)->execute();
		$this->assertEqual(2, $this->remoteService->getPostNoticeCallCount());
	}
	
	public function testStrategyWithTwoAccessTokensWithTheSamePostNoticeUrl() {
		$accessTokens = array($this->createAccessToken('http://example.com'),
							  $this->createAccessToken('http://example.com'));
		$this->createStrategy($accessTokens)->execute();
		$this->assertEqual(1, $this->remoteService->getPostNoticeCallCount());
	}
	
	private function createAccessToken($postNoticeUrl) {
		return array('OmbLocalService' => array('post_notice_url' => $postNoticeUrl),
					 'OmbLocalServiceAccessToken' => array('token_key' => 'key', 'token_secret' => 'secret'));
	}
	
	private function createStrategy(array $accessTokens) {
		$notice = new OmbNotice(1, 'A notice');
		return new OmbPostNoticeStrategy($this->remoteService, $accessTokens, $notice);
	}
}