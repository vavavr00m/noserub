<?php
	App::import('Controller', 'App');
	
	class OpenidHelperTest extends CakeTestCase {
		var $helper = null;
		var $url = null;
		
		function setUp() {
			$this->helper = new OpenidHelper();
			$this->url = FULL_BASE_URL.'/server';
		}
		
		function testDelegate() {
			$expected = '<link rel="openid.delegate" href="'.$this->url.'" />';
			$result = $this->helper->delegate($this->url);
			$this->assertEqual($expected, $result);
		}
		
		function testDelegateNotInline() {
			$view = $this->createView();
			$this->helper->delegate($this->url, false);
			$renderedLayout = $view->renderLayout('');
			$this->assertPattern('#<head>.*?<link rel="openid.delegate" href="'.$this->url.'" />.*?</head>#s', $renderedLayout);
		}
		
		function testServerLink() {
			$result = $this->helper->serverLink($this->url);
			$this->assertEqual($this->getServerLink(), $result);
		}
		
		function testServerLinkWithRelativeUrl() {
			$result = $this->helper->serverLink('/server');
			$this->assertEqual($this->getServerLink(), $result);
		}
		
		function testServerLinkNotInline() {
			$view = $this->createView();
			$this->helper->serverLink($this->url, false);
			$renderedLayout = $view->renderLayout('');
			$this->assertPattern('#<head>.*?'.$this->getServerLink().'.*?</head>#s', $renderedLayout);
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
			$view = $this->createView();
			$this->helper->xrdsLocation($this->url, false);
			$renderedLayout = $view->renderLayout('');
			$this->assertPattern('#<head>.*?<meta http-equiv="X-XRDS-Location" content="'.$this->url.'" />.*?</head>#si', $renderedLayout);
		}
		
		private function createView() {
			// required because of https://trac.cakephp.org/ticket/3241
			ClassRegistry::removeObject('view');
			
			App::import('Model', 'Menu');
			$view = new View(new AppController());
        	$view->set('mainMenu', new Menu(array()));
        	$view->set('subMenu', new Menu(array()));
			$view->set('menu', array('main' => '',
                      	 			 'logged_in' => ''));
			
			return $view;
		}
		
		private function getServerLink() {
			return '<link rel="openid2.provider openid.server" href="'.$this->url.'" />';
		}
	}
?>