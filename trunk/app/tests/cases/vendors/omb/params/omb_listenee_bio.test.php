<?php
App::import('Vendor', 'OmbListeneeBio');

class OmbListeneeBioTest extends CakeTestCase {
	public function testConstruct() {
		$bio = 'My bio';
		$listeneeBio = new OmbListeneeBio($bio);
		$this->assertEqual(OmbParamKeys::LISTENEE_BIO, $listeneeBio->getKey());
		$this->assertEqual($bio, $listeneeBio->getValue());
	}
	
	public function testConstructWithTooLongBio() {
		$bio = str_repeat('a', OmbListeneeBio::MAX_LENGTH + 1);
		$listeneeBio = new OmbListeneeBio($bio);
		$this->assertEqual(OmbListeneeBio::MAX_LENGTH, strlen($listeneeBio->getValue()));
	}
}