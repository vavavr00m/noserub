<?php
App::import('Component', 'Geocoder');

# it does not make sense to test for equality, as
# the accuracy may change and therefore the test woul
# fail, although the address is still roughly the same.

class GeocoderComponentTestCase extends CakeTestCase {
    private $component = null;
    
	public function setUp() {
		$this->component = new GeocoderComponent();
	}
	
	public function skip() {
		$this->skipif (!NOSERUB_GOOGLE_MAPS_KEY, 'NOSERUB_GOOGLE_MAPS_KEY not set in noserub.php');
	}
	
	public function testGet1() {
	    $address = 'Kölnstraße 129, 53111 Bonn, Deutschland';
	    $address = 'Eupener Straße, Köln';
	    
	    $result = $this->component->get($address);
	    
	    $this->assertEqual(round($result['latitude'], 3), 50.942);
	    $this->assertEqual(round($result['longitude'], 3), 6.889);
	}
	
	public function testGet2() {
	    $address = 'Kölnstraße 129, 53111 Bonn, Deutschland';
	    
	    $result = $this->component->get($address);
	    
	    $this->assertEqual(round($result['latitude'], 3), 50.742);
	    $this->assertEqual(round($result['longitude'], 3), 7.095);
	}
	
	public function testGet3() {
	    $address = 'an unknown stadt';
	    
	    $result = $this->component->get($address);
	    $this->assertFalse($result);
	}
	
	public function testDistance() {
	    $this->assertEqual(7.189, round($this->component->distance(50.740081, 7.098094, 50.788847, 7.165207), 3));
	}
	
	public function tearDown() {
	    unset($this->component);
	}
}	