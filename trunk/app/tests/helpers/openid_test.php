<?php
	loadController(null);

	class OpenidHelperTest extends CakeTestCase {
		var $helper = null;
		var $url = 'http://example.com/server';
		
		function setUp() {
			$this->helper = new OpenidHelper();
		}
		
		function testServerLink() {
			$expected = '<link rel="openid.server" href="'.$this->url.'" />';
			$result = $this->helper->serverLink($this->url);
			$this->assertEqual($expected, $result);
		}
		
		function testServerLinkWithRelativeUrl() {
			define('FULL_BASE_URL', 'http://example.com');
			$expected = '<link rel="openid.server" href="'.$this->url.'" />';
			$result = $this->helper->serverLink('/server');
			$this->assertEqual($expected, $result);
		}
		
		function testServerLinkNotInline() {
			// required because of https://trac.cakephp.org/ticket/3241
			ClassRegistry::removeObject('view');
			$view = new View(new AppController());
			$this->helper->serverLink($this->url, false);
			$renderedLayout = $view->renderLayout('');
			$this->assertPattern('#<head>(.)*<link rel="openid.server" href="'.$this->url.'" />(.)*</head>#si', $renderedLayout);
		}
		
		function testXrdsLocation() {
			$expected = '<meta http-equiv="X-XRDS-Location" content="'.$this->url.'" />';
			$result = $this->helper->xrdsLocation($this->url);
			$this->assertEqual($expected, $result);
		}
		
		function testXrdsLocationWithRelativeUrl() {
			define('FULL_BASE_URL', 'http://example.com');
			$expected = '<meta http-equiv="X-XRDS-Location" content="'.$this->url.'" />';
			$result = $this->helper->xrdsLocation('/server');
			$this->assertEqual($expected, $result);
		}
		
		function testXrdsLocationNotInline() {
			// required because of https://trac.cakephp.org/ticket/3241
			ClassRegistry::removeObject('view');
			$view = new View(new AppController());
			$this->helper->xrdsLocation($this->url, false);
			$renderedLayout = $view->renderLayout('');
			$this->assertPattern('#<head>(.)*<meta http-equiv="X-XRDS-Location" content="'.$this->url.'" />(.)*</head>#si', $renderedLayout);
		}
	}
?>