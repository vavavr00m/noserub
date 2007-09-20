<?php

class JsonComponentTestCase extends CakeTestCase {
    var $component = null;
    
	function setUp() {
		$this->component = new JsonComponent();
	}
	
	function testJsonEncodeDecode() {
	    $value = array(2, 3, 'hallo', 4 => 'test');
	    $encoded = $this->component->encode($value);
	    $decoded = $this->component->decode($encoded);
	    $this->assertEqual($value, $decoded);
	}
		
	function tearDown() {
	    unset($this->component);
	}
}	