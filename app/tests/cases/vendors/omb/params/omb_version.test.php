<?php
App::import('Vendor', 'OmbVersion');

class OmbVersionTest extends CakeTestCase {
	public function testConstruct() {
		$version = new OmbVersion();
		$this->assertEqual(OmbParamKeys::VERSION, $version->getKey());
		$this->assertEqual(OmbConstants::VERSION, $version->getValue());
	}
}