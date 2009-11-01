<?php
App::import('Vendor', 'OmbListeneeLocation');

class OmbListeneeLocationTest extends CakeTestCase {
	public function testConstruct() {
		$location = 'MyCity, MyCountry';
		$listeneeLocation = new OmbListeneeLocation($location);
		$this->assertEqual(OmbParamKeys::LISTENEE_LOCATION, $listeneeLocation->getKey());
		$this->assertEqual($location, $listeneeLocation->getValue());
	}
	
	public function testConstructWithTooLongLocation() {
		$location = str_repeat('a', OmbListeneeLocation::MAX_LENGTH + 1);
		$listeneeLocation = new OmbListeneeLocation($location);
		$this->assertEqual(OmbListeneeLocation::MAX_LENGTH, strlen($listeneeLocation->getValue()));
	}
}