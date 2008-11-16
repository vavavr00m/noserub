<?php

App::import('Model', 'OmbService');

class OmbServiceTest extends CakeTestCase {
	private $service = null;
	
	public function setUp() {
		$this->service = new OmbService();
	}
	
	public function testValidation() {
		$this->service->create($this->getData('http://example.com', 'http://example.net'));
		$this->assertValid($this->service->validates());
		
		$this->service->create($this->getData('invalid', 'http://example.com'));
		$this->assertInvalid($this->service->validates());
		
		$this->service->create($this->getData('http://example.com', 'invalid'));
		$this->assertInvalid($this->service->validates());
		
		$this->service->create($this->getData('', ''));
		$this->assertInvalid($this->service->validates());
	}
	
	private function assertInvalid($validationResult) {
		$this->assertIdentical(false, $validationResult);
	}
	
	private function assertValid($validationResult) {
		$this->assertIdentical(true, $validationResult);
	}
	
	private function getData($postNoticeUrl, $updateProfileUrl) {
		return array('OmbService' => array('post_notice_url' => $postNoticeUrl, 
										   'update_profile_url' => $updateProfileUrl));
	}
}