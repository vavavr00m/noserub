<?php

class CdnComponentTestCase extends CakeTestCase {
    var $component = null;
    
	function setUp() {
		$this->component = new CdnComponent();
	}
	
	function skip() {
	    $should_skip = defined('NOSERUB_USE_CDN') == false || NOSERUB_USE_CDN == false;
		$this->skipif($should_skip, 'NOSERUB_USE_CDN not set to "true" in noserub.php');
	}
	
	function testGetBuckets() {
	    $result = $this->component->getBuckets();
	    # at least the noser-bucket should be there
	    $this->assertTrue(isset($result[NOSERUB_CDN_S3_BUCKET]));
	}
		
	function testFileOperations() {
	    $path = 'tmp/test/' . rand(10000, 99999);
	    $content = 'nur ein kleiner test: ' . rand(10000, 99999);
	    $this->component->writeContent($path, 'text/plain', $content);
	    
	    # test, that it was created
	    $found = false;
	    $result = $this->component->listBucket();
	    foreach($result as $name => $object) {
	        if($name == $path) {
	            $object->Get();
	            $data = $object->GetData();
	            $this->assertTrue($data == $content);
	            $object->Delete();
                $found = true;
	        }
	    }
	    
	    $this->assertTrue($found);

        # test, that it really was deleted
        $found = false;
	    $result = $this->component->listBucket();
	    foreach($result as $name => $object) {
	        if($name == $path) {
                $found = true;
	        }
	    }

        $this->assertFalse($found);
	}
	
	function tearDown() {
	    unset($this->component);
	}
}	