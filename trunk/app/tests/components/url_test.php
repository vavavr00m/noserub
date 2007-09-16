<?php

class UrlComponentTestCase extends CakeTestCase {
    var $component = null;
    
	function setUp() {
		$this->component = new UrlComponent();
	}
	
	function testHttpEmpty() {
	    $url = '';
	    $this->assertEqual($url, $this->component->http($url));
	}
	
	function testHttpNull() {
	    $url = null;
	    $this->assertEqual($url, $this->component->http($url));
	}
	
    function testHttpHttps() {
	    $url = 'https://identoo.com/';
	    $this->assertEqual('http://identoo.com/', $this->component->http($url));
	}
	
	function testHttpHttp() {
	    $url = 'http://identoo.com/';
	    $this->assertEqual('http://identoo.com/', $this->component->http($url));
	}
	
	function testHttpRelative() {
	    $url = '/settings/';
	    $expected = FULL_BASE_URL . Router::url($url);
	    $this->assertEqual($expected, $this->component->http($url));
	}
	
	function tearDown() {
	    unset($this->component);
	}
}	