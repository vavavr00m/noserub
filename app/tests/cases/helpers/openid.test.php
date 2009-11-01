<?php
App::import('Controller', 'App');
App::import('Helper', 'Openid');

class OpenidHelperTest extends CakeTestCase {
	private $helper = null;
	private $url = null;
	
	public function setUp() {
		$this->helper = new OpenidHelper();
		$this->url = FULL_BASE_URL.'/server';
	}
	
	public function testDelegate() {
		$expected = '<link rel="openid.delegate" href="'.$this->url.'" />';
		$result = $this->helper->delegate($this->url);
		$this->assertEqual($expected, $result);
	}
	
	public function testDelegateNotInline() {
		$view = $this->createView();
		$this->helper->delegate($this->url, false);
		$renderedLayout = $view->renderLayout('');
		$this->assertPattern('#<head>.*?<link rel="openid.delegate" href="'.$this->url.'" />.*?</head>#s', $renderedLayout);
	}
	
	public function testServerLink() {
		$result = $this->helper->serverLink($this->url);
		$this->assertEqual($this->getServerLink(), $result);
	}
	
	public function testServerLinkWithRelativeUrl() {
		$result = $this->helper->serverLink('/server');
		$this->assertEqual($this->getServerLink(), $result);
	}
	
	public function testServerLinkNotInline() {
		$view = $this->createView();
		$this->helper->serverLink($this->url, false);
		$renderedLayout = $view->renderLayout('');
		$this->assertPattern('#<head>.*?'.$this->getServerLink().'.*?</head>#s', $renderedLayout);
	}
	
	public function testXrdsLocation() {
		$expected = '<meta http-equiv="X-XRDS-Location" content="'.$this->url.'" />';
		$result = $this->helper->xrdsLocation($this->url);
		$this->assertEqual($expected, $result);
	}
	
	public function testXrdsLocationWithRelativeUrl() {
		$expected = '<meta http-equiv="X-XRDS-Location" content="'.$this->url.'" />';
		$result = $this->helper->xrdsLocation('/server');
		$this->assertEqual($expected, $result);
	}
	
	public function testXrdsLocationNotInline() {
		$view = $this->createView();
		$this->helper->xrdsLocation($this->url, false);
		$renderedLayout = $view->renderLayout('');
		$this->assertPattern('#<head>.*?<meta http-equiv="X-XRDS-Location" content="'.$this->url.'" />.*?</head>#si', $renderedLayout);
	}
	
	private function createView() {
		// required because of https://trac.cakephp.org/ticket/3241
		ClassRegistry::removeObject('view');
		
		$view = new View(new AppController());
		
		require_once(TESTS.'util'.DS.'helper_factory.php');
		$view->set('form', HelperFactory::createFormHelper());
		
		return $view;
	}
	
	private function getServerLink() {
		return '<link rel="openid2.provider openid.server" href="'.$this->url.'" />';
	}
}