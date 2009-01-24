<?php
App::import('Vendor', 'OmbListenee');

class OmbListeneeTest extends CakeTestCase {
	public function testConstruct() {
		$listenee = 'http://example.com/listenee';
		$ombListenee = new OmbListenee($listenee);
		$this->assertEqual(OmbParamKeys::LISTENEE, $ombListenee->getKey());
		$this->assertEqual($listenee, $ombListenee->getValue());
	}
}