<?php
App::import('Component', 'SocialGraph');

class SocialGraphComponentTestCase extends CakeTestCase {
    private $component = null;
    
	public function setUp() {
		$this->component = new SocialGraphComponent();
	}
	
	public function skip() {
		$this->skipif(true, 'FIXME');
	}
	
	public function testLookup() {
        $url = 'http://identoo.com/dirk.olbertz';
        $result = $this->component->lookup($url);
        $this->assertTrue(in_array('http://olbertz.de/blog', $result['nodes']['http://identoo.com/dirk.olbertz']['claimed_nodes']));
	}
		
	public function tearDown() {
	    unset($this->component);
	}
}	