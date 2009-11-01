<?php
App::import('Vendor', 'OmbListeneeFullname');

class OmbListeneeFullnameTest extends CakeTestCase {
	public function testConstruct() {
		$fullname = 'Firstname Lastname';
		$listeneeFullname = new OmbListeneeFullname($fullname);
		$this->assertEqual(OmbParamKeys::LISTENEE_FULLNAME, $listeneeFullname->getKey());
		$this->assertEqual($fullname, $listeneeFullname->getValue());
	}
	
	public function testConstructWithTooLongFullname() {
		$fullname = str_repeat('a', OmbListeneeFullname::MAX_LENGTH + 1);
		$listeneeFullname = new OmbListeneeFullname($fullname);
		$this->assertEqual(OmbListeneeFullname::MAX_LENGTH, strlen($listeneeFullname->getValue()));
	}
}