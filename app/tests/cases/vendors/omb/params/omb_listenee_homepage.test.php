<?php
App::import('Vendor', 'OmbListeneeHomepage');

class OmbListeneeHomepageTest extends CakeTestCase {
	public function testConstruct() {
		$homepage = 'http://example.com';
		$listeneeHomepage = new OmbListeneeHomepage($homepage);
		$this->assertEqual(OmbParamKeys::LISTENEE_HOMEPAGE, $listeneeHomepage->getKey());
		$this->assertEqual($homepage, $listeneeHomepage->getValue());
	}
}