<?php
App::import('Component', 'Json');

class JsonComponentTestCase extends CakeTestCase {
    private $component = null;
    
	public function setUp() {
		$this->component = new JsonComponent();
	}
	
	public function testJsonEncodeDecode() {
	    $value = array(2, 3, 'hallo', 4 => 'test');
	    $encoded = $this->component->encode($value);
	    $decoded = $this->component->decode($encoded);
	    $this->assertEqual($value, $decoded);
	}
		
	public function tearDown() {
	    unset($this->component);
	}
}	