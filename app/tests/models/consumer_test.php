<?php

class ConsumerTest extends CakeTestCase {
	private $consumer = null;
	
	public function setUp() {
		$this->consumer = new Consumer();
	}
	
	public function testValidationOfOptionalCallbackUrl() {
		$this->consumer->create($this->getConsumerData('http://example.com'));
		$this->assertIdentical(true, $this->consumer->validates());
		
		$this->consumer->create($this->getConsumerData(''));
		$this->assertIdentical(true, $this->consumer->validates());
		
		$this->consumer->create($this->getConsumerData('invalid_url'));
		$this->assertIdentical(false, $this->consumer->validates());
	}
	
	private function getConsumerData($url) {
		return array('Consumer' => array('callback_url' => $url));
	}
}