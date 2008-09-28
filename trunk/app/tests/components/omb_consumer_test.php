<?php

define('IDENTICA', 'http://identi.ca');

class OmbConsumerComponentTest extends CakeTestCase {
	private $component = null;
	
	public function setUp() {
		$this->component = new OmbConsumerComponent();
	}
	
	public function testDiscover() {
		$endPoints = $this->component->discover(NOSERUB_FULL_BASE_URL.DS.'testing'.DS.'identica_0.6.xrds');
		$this->assertEqual(2, count($endPoints));
		$this->assertEqual(IDENTICA.'/user/4599', $endPoints[0]);
		$this->assertEqual(5, count($endPoints[1]));
		$this->assertEqual(IDENTICA.'/index.php?action=requesttoken', $endPoints[1][OAUTH_REQUEST]);
		$this->assertEqual(IDENTICA.'/index.php?action=userauthorization', $endPoints[1][OAUTH_AUTHORIZE]);
		$this->assertEqual(IDENTICA.'/index.php?action=accesstoken', $endPoints[1][OAUTH_ACCESS]);
		$this->assertEqual(IDENTICA.'/index.php?action=postnotice', $endPoints[1][OMB_POST_NOTICE]);
		$this->assertEqual(IDENTICA.'/index.php?action=updateprofile', $endPoints[1][OMB_UPDATE_PROFILE]);
	}
	
	public function testDiscoverNotExistingFile() {
		try {
			$this->component->discover(NOSERUB_FULL_BASE_URL.DS.'testing'.DS.'notexisting');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}
}