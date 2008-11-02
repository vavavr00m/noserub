<?php

App::import('Model', 'OmbService');

class OmbServiceTest extends CakeTestCase {
	private $service = null;
	
	public function setUp() {
		$this->service = new OmbService();
	}
	
	public function skip() {
		$this->skipIf(true, 'Validation rules are disabled in OmbService');
	}
	
	public function testValidation() {
		$this->service->create($this->getData('http://example.com', 'http://example.net'));
		$this->assertIdentical(true, $this->service->validates());
		
		$this->service->create($this->getData('invalid', 'http://example.com'));
		$this->assertIdentical(false, $this->service->validates());
		
		$this->service->create($this->getData('http://example.com', 'invalid'));
		$this->assertIdentical(false, $this->service->validates());
		
		$this->service->create($this->getData('', ''));
		$this->assertIdentical(false, $this->service->validates());
	}
	
	private function getData($postNoticeUrl, $updateProfileUrl) {
		return array('OmbService' => array('post_notice_url' => $postNoticeUrl, 
										   'update_profile_url' => $updateProfileUrl));
	}
}