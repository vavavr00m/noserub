<?php
App::import('Component', 'OmbRemoteService');

class MockRemoteServiceComponent extends OmbRemoteServiceComponent {
	private $postNoticeCallCount = 0;
	private $updateProfileCallCount = 0;
	
	public function postNoticeToUrl($tokenKey, $tokenSecret, $url, OmbNotice $ombNotice) {
		$this->postNoticeCallCount++;
	}
	
	public function updateProfileToUrl($tokenKey, $tokenSecret, $url, OmbUpdatedProfileData $profileData) {
		$this->updateProfileCallCount++;
	}
	
	public function getPostNoticeCallCount() {
		return $this->postNoticeCallCount;
	}
	
	public function getUpdateProfileCallCount() {
		return $this->updateProfileCallCount;
	}
}