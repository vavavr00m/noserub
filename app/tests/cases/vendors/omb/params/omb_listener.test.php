<?php
App::import('Vendor', 'OmbListener');

class OmbListenerTest extends CakeTestCase {
	public function testConstruct() {
		$listener = 'http://example.com/listener';
		$ombListener = new OmbListener($listener);
		$this->assertEqual(OmbParamKeys::LISTENER, $ombListener->getKey());
		$this->assertEqual($listener, $ombListener->getValue()); 
	}
}