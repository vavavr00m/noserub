<?php
App::import('Component', 'OmbRemoteService');
App::import('Vendor', array('OauthConstants', 'OmbConstants', 'OmbParamKeys'));

class OmbRemoteServiceComponentTest extends CakeTestCase {
	const IDENTICA = 'http://identi.ca';
	private $component = null;
	private $urlOfXRDS = null;
	
	public function setUp() {
		$this->component = new OmbRemoteServiceComponent();
		$this->urlOfXRDS = Configure::read('context.network.url') . 'testing/identica_0.6.xrds';
	}
	
	public function testDiscoverLocalService() {
		$this->assertLocalService($this->component->discoverLocalService($this->urlOfXRDS));
	}
	
	public function testDiscoverLocalServiceFromUrlWithoutHttp() {
		$urlOfXRDSWithoutHttp = str_replace('http://', '', $this->urlOfXRDS);
		$this->assertLocalService($this->component->discoverLocalService($urlOfXRDSWithoutHttp));
	}
	
	public function testDiscoverLocalServiceFromNonExistingUrl() {
		try {
			$this->component->discoverLocalService(Configure::read('context.network.url') . 'testing/notexisting');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}
	
	private function assertLocalService(OmbLocalServiceDefinition $localService) {
		$this->assertEqual(self::IDENTICA.'/user/4599', $localService->getLocalId());
		$this->assertEqual(self::IDENTICA.'/index.php?action=requesttoken', $localService->getRequestTokenUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=userauthorization', $localService->getAuthorizeUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=accesstoken', $localService->getAccessTokenUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=postnotice', $localService->getPostNoticeUrl());
		$this->assertEqual(self::IDENTICA.'/index.php?action=updateprofile', $localService->getUpdateProfileUrl());
	}
}