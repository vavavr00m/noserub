<?php

class SocialGraphComponentTestCase extends CakeTestCase {
    var $component = null;
    
	function setUp() {
		$this->component = new SocialGraphComponent();
	}
	
	function skip() {
		$this->skipif(true, 'FIXME');
	}
	
	function testLookup() {
        $url = 'http://identoo.com/dirk.olbertz';
        $result = $this->component->lookup($url);
        $this->assertTrue(in_array('http://olbertz.de/blog', $result['nodes']['http://identoo.com/dirk.olbertz']['claimed_nodes']));
	}
		
	function tearDown() {
	    unset($this->component);
	}
}	