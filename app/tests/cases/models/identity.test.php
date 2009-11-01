<?php
	
class IdentityModelTestCase extends CakeTestCase {

	public function setUp() {
	    App::import('Model', 'Identity');
		$this->model = new Identity();
	}
	
	# splitUsername($username)
	public function testSplitUsernameLocal() {
		$server_base = $this->getServerBase();
	    $expected = $this->getDirksLocalUsername($server_base);
	    $result = $this->model->splitUsername('dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	public function testSplitUsernameLocalExtended() {
		$server_base = $this->getServerBase();
	    $expected = $this->getDirksLocalUsername($server_base);
	    $result = $this->model->splitUsername($server_base . '/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	public function testSplitUsernameLocalNamespace() {
		$server_base = $this->getServerBase();
	    
	    $expected = array('username'        => $server_base . '/poolie@dirk.olbertz',
	                      'local_username'  => 'poolie@dirk.olbertz',
	                      'single_username' => 'poolie',
	                      'namespace'       => 'dirk.olbertz',
	                      'servername'      => $server_base,
	                      'local'           => 1);
	                      
	    $result = $this->model->splitUsername('poolie@dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	public function testSplitUsernameExtern() {
	    $expected = array('username'        => 'identoo.com/dirk.olbertz',
	                      'local_username'  => 'dirk.olbertz',
	                      'single_username' => 'dirk.olbertz',
	                      'namespace'       => '',
	                      'servername'      => 'identoo.com',
	                      'local'           => 0);
	                      
	    $result = $this->model->splitUsername('identoo.com/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	public function testSplitUsernameExternPath() {
	    $expected = array('username'        => 'identoo.com/noserub/dirk.olbertz',
	                      'local_username'  => 'dirk.olbertz',
	                      'single_username' => 'dirk.olbertz',
	                      'namespace'       => '',
	                      'servername'      => 'identoo.com',
	                      'local'           => 0);
	                      
	    $result = $this->model->splitUsername('identoo.com/noserub/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	public function testSplitUsernameWithHttp() {
	    $server_base = $this->getServerBase();
	    $expected = $this->getDirksLocalUsername($server_base);
	    $result = $this->model->splitUsername('http://' . $server_base . '/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	public function testSplitUsernameWithHttps() {
		$server_base = $this->getServerBase();
	    $expected = $this->getDirksLocalUsername($server_base);
	    $result = $this->model->splitUsername('https://' . $server_base . '/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	public function testSplitUsernameWithWww() {
	    $server_base = $this->getServerBase();
	    $expected = $this->getDirksLocalUsername($server_base);
	    $result = $this->model->splitUsername('www.' . $server_base . '/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	public function testSplitUsernameWithHttpAndWww() {
		$server_base = $this->getServerBase();
	    $expected = $this->getDirksLocalUsername($server_base);
	    $result = $this->model->splitUsername('http://www.' . $server_base . '/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	public function testSplitUsernameWithHttpsAndWww() {
	    $server_base = $this->getServerBase();
	    $expected = $this->getDirksLocalUsername($server_base);
	    $result = $this->model->splitUsername('https://www.' . $server_base . '/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	public function tearDown() {
	    unset($this->model);
	}
	
	private function getDirksLocalUsername($server_base) {
		return array('username'        => $server_base . '/dirk.olbertz',
	                 'local_username'  => 'dirk.olbertz',
	                 'single_username' => 'dirk.olbertz',
	                 'namespace'       => '',
	                 'servername'      => $server_base,
	                 'local'           => 1);
	}
	
	private function getServerBase() {
		App::import('Vendor', 'UrlUtil');
		$server_base = UrlUtil::removeHttpAndHttps(FULL_BASE_URL . Router::url('/'));
	    $server_base = trim($server_base, '/');
	    
	    return $server_base;
	}
}