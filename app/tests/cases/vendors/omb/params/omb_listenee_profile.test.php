<?php
App::import('Vendor', 'OmbListeneeProfile');

class OmbListeneeProfileTest extends CakeTestCase {
	public function testConstruct() {
		$profile = 'http://example.com/profile';
		$listeneeProfile = new OmbListeneeProfile($profile);
		$this->assertEqual(OmbParamKeys::LISTENEE_PROFILE, $listeneeProfile->getKey());
		$this->assertEqual($profile, $listeneeProfile->getValue());
	}
}