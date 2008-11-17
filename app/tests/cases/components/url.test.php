<?php
App::import('Component', 'Url');

class UrlComponentTestCase extends CakeTestCase {
    private $component = null;
    
	public function setUp() {
		$this->component = new UrlComponent();
	}
	
	public function testHttpEmpty() {
	    $url = '';
	    $this->assertEqual($url, $this->component->http($url));
	}
	
	public function testHttpNull() {
	    $url = null;
	    $this->assertEqual($url, $this->component->http($url));
	}
	
    public function testHttpHttps() {
	    $url = 'https://identoo.com/';
	    $this->assertEqual('http://identoo.com/', $this->component->http($url));
	}
	
	public function testHttpHttp() {
	    $url = 'http://identoo.com/';
	    $this->assertEqual('http://identoo.com/', $this->component->http($url));
	}
	
	public function testHttpRelative() {
	    $url = '/settings/';
	    $expected = FULL_BASE_URL . Router::url($url);
	    $this->assertEqual($expected, $this->component->http($url));
	}
	
	public function testStartsWithHttpOrHttps() {
		$this->assertIdentical(true, $this->component->startsWithHttpOrHttps('http://example.com'));
		$this->assertIdentical(true, $this->component->startsWithHttpOrHttps('https://example.com'));
		$this->assertIdentical(false, $this->component->startsWithHttpOrHttps('example.com'));
		$this->assertIdentical(false, $this->component->startsWithHttpOrHttps('example.com?url=http://example.net'));
	}
	
	public function tearDown() {
	    unset($this->component);
	}
}	