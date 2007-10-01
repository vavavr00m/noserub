<?php
	loadController(null);
	
	class OpenidHelperTest extends CakeTestCase {
		var $helper = null;
		var $url = null;
		
		function setUp() {
			$this->helper = new OpenidHelper();
			$this->url = FULL_BASE_URL.'/server';
		}
		
		function testServerLink() {
			$expected = '<link rel="openid.server" href="'.$this->url.'" />';
			$result = $this->helper->serverLink($this->url);
			$this->assertEqual($expected, $result);
		}
		
		function testServerLinkWithRelativeUrl() {
			$expected = '<link rel="openid.server" href="'.$this->url.'" />';
			$result = $this->helper->serverLink('/server');
			$this->assertEqual($expected, $result);
		}
		
		function testServerLinkNotInline() {
			$view = $this->__createView();
			$this->helper->serverLink($this->url, false);
			$renderedLayout = $view->renderLayout('');
			$this->assertPattern('#<head>.*?<link rel="openid.server" href="'.$this->url.'" />.*?</head>#s', $renderedLayout);
		}
		
		function testXrdsLocation() {
			$expected = '<meta http-equiv="X-XRDS-Location" content="'.$this->url.'" />';
			$result = $this->helper->xrdsLocation($this->url);
			$this->assertEqual($expected, $result);
		}
		
		function testXrdsLocationWithRelativeUrl() {
			$expected = '<meta http-equiv="X-XRDS-Location" content="'.$this->url.'" />';
			$result = $this->helper->xrdsLocation('/server');
			$this->assertEqual($expected, $result);
		}
		
		function testXrdsLocationNotInline() {
			$view = $this->__createView();
			$this->helper->xrdsLocation($this->url, false);
			$renderedLayout = $view->renderLayout('');
			$this->assertPattern('#<head>.*?<meta http-equiv="X-XRDS-Location" content="'.$this->url.'" />.*?</head>#si', $renderedLayout);
		}
		
		function __createView() {
			// required because of https://trac.cakephp.org/ticket/3241
			ClassRegistry::removeObject('view');
			
			$view = new View(new AppController());
        	$view->set('menu', array('main' => '',
                      	 			 'sub'  => '',
                      	 			 'model'     => '',
                      	 			 'action'    => '',
                   		 			 'filter'    => '',
                      	 			 'logged_in' => ''));
			
			return $view;
		}
	}
?>