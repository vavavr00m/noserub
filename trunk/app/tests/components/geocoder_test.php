<?php
loadController(null);

# it does not make sense to test for equality, as
# the accuracy may change and therefore the test woul
# fail, although the address is still roughly the same.

class GeocoderComponentTestCase extends CakeTestCase {
    var $component = null;
    
	function setUp() {
		$this->component = new GeocoderComponent();
	}
	
	function testGet1() {
	    $address = 'Kölnstraße 129, 53111 Bonn, Deutschland';
	    $address = 'Eupener Straße, Köln';
	    
	    $result = $this->component->get($address);
	    
	    $this->assertEqual(round($result['latitude'], 3), 50.942);
	    $this->assertEqual(round($result['longitude'], 3), 6.889);
	}
	
	function testGet2() {
	    $address = 'Kölnstraße 129, 53111 Bonn, Deutschland';
	    
	    $result = $this->component->get($address);
	    
	    $this->assertEqual(round($result['latitude'], 3), 50.742);
	    $this->assertEqual(round($result['longitude'], 3), 7.095);
	}
	
	function testGet3() {
	    $address = 'fabelstadt';
	    
	    $result = $this->component->get($address);
	    $this->assertFalse($result);
	}
	
	function tearDown() {
	    unset($this->component);
	}
}	