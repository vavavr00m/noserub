<?php
loadController(null);
	
class IdentityModelTest extends CakeTestCase {

	function setUp() {
	    loadModel('Identity');
		$this->model = new Identity();
	}
	
	function testSplitUsername() {
	    $server_base = str_replace('http://', '', FULL_BASE_URL . Router::url('/'));
	    $server_base = str_replace('https://', '', $server_base);
	    
	    $expected = array('username'       => $server_base . '/dirk.olbertz',
	                      'local_username' => 'dirk.olbertz',
	                      'namespace'      => '',
	                      'local'          => 1);
	                      
	    $result = $this->model->splitUsername('dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	function tearDown() {
	    unset($this->model);
	}
}
?>