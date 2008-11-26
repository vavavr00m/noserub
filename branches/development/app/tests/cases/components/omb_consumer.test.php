<?php
App::import('Component', 'OmbConsumer');
App::import('Vendor', 'OmbConstants');
App::import('Vendor', 'OauthConstants');

class OmbConsumerComponentTest extends CakeTestCase {
	const IDENTICA = 'http://identi.ca';
	private $component = null;
	
	public function setUp() {
		$this->component = new OmbConsumerComponent();
	}
	
	public function testDiscover() {
		$endPoints = $this->component->discover(Configure::read('NoseRub.full_base_url') . 'testing/identica_0.6.xrds');
		$this->assertEqual(2, count($endPoints));
		$this->assertEqual(self::IDENTICA.'/user/4599', $endPoints[0]);
		$this->assertEqual(5, count($endPoints[1]));
		$this->assertEqual(self::IDENTICA.'/index.php?action=requesttoken', $endPoints[1][OauthConstants::REQUEST]);
		$this->assertEqual(self::IDENTICA.'/index.php?action=userauthorization', $endPoints[1][OauthConstants::AUTHORIZE]);
		$this->assertEqual(self::IDENTICA.'/index.php?action=accesstoken', $endPoints[1][OauthConstants::ACCESS]);
		$this->assertEqual(self::IDENTICA.'/index.php?action=postnotice', $endPoints[1][OmbConstants::POST_NOTICE]);
		$this->assertEqual(self::IDENTICA.'/index.php?action=updateprofile', $endPoints[1][OmbConstants::UPDATE_PROFILE]);
	}
	
	public function testDiscoverNotExistingFile() {
		try {
			$this->component->discover(Configure::read('NoseRub.full_base_url') . 'testing/notexisting');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}
}