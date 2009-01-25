<?php
App::import('Vendor', 'OmbListeneeLicense');

class OmbListeneeLicenseTest extends CakeTestCase {
	public function testConstruct() {
		$expectedLicense = OmbListeneeLicense::CREATIVE_COMMONS;
		$listeneeLicense = new OmbListeneeLicense();
		$this->assertEqual(OmbParamKeys::LISTENEE_LICENSE, $listeneeLicense->getKey());
		$this->assertEqual($expectedLicense, $listeneeLicense->getValue());
	}
}