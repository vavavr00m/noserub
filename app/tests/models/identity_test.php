<?php
	
class IdentityModelTestCase extends CakeTestCase {

	function setUp() {
	    App::import('Model', 'Identity');
		$this->model = new Identity();
	}
	
	# removeHttpWww($url)
	function testRemoveHttpWww() {
	    $tests = array('http://identoo.com/dirk.olbertz'      => 'identoo.com/dirk.olbertz',
	                   'https://identoo.com/dirk.olbertz'     => 'identoo.com/dirk.olbertz',
	                   'http://www.identoo.com/dirk.olbertz'  => 'identoo.com/dirk.olbertz',
	                   'https://www.identoo.com/dirk.olbertz' => 'identoo.com/dirk.olbertz',
	                   'http://www.www.test.com/www.olbertz'  => 'www.test.com/www.olbertz');
	
	    foreach($tests as $before => $after) {
	        $this->assertEqual($after, $this->model->removeHttpWww($before));
	    }
	}
	
	# splitUsername($username)
	function testSplitUsernameLocal() {
	    $server_base = str_replace('http://', '', FULL_BASE_URL . Router::url('/'));
	    $server_base = str_replace('https://', '', $server_base);
	    $server_base = trim($server_base, '/');
	    
	    $expected = array('username'        => $server_base . '/dirk.olbertz',
	                      'local_username'  => 'dirk.olbertz',
	                      'single_username' => 'dirk.olbertz',
	                      'namespace'       => '',
	                      'servername'      => $server_base,
	                      'local'           => 1);
	                      
	    $result = $this->model->splitUsername('dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	function testSplitUsernameLocalExtended() {
	    $server_base = str_replace('http://', '', FULL_BASE_URL . Router::url('/'));
	    $server_base = str_replace('https://', '', $server_base);
	    $server_base = trim($server_base, '/');
	    
	    $expected = array('username'        => $server_base . '/dirk.olbertz',
	                      'local_username'  => 'dirk.olbertz',
	                      'single_username' => 'dirk.olbertz',
	                      'namespace'       => '',
	                      'servername'      => $server_base,
	                      'local'           => 1);
	                      
	    $result = $this->model->splitUsername($server_base . '/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	function testSplitUsernameLocalNamespace() {
	    $server_base = str_replace('http://', '', FULL_BASE_URL . Router::url('/'));
	    $server_base = str_replace('https://', '', $server_base);
	    $server_base = trim($server_base, '/');
	    
	    $expected = array('username'        => $server_base . '/poolie@dirk.olbertz',
	                      'local_username'  => 'poolie@dirk.olbertz',
	                      'single_username' => 'poolie',
	                      'namespace'       => 'dirk.olbertz',
	                      'servername'      => $server_base,
	                      'local'           => 1);
	                      
	    $result = $this->model->splitUsername('poolie@dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	function testSplitUsernameExtern() {
	    $expected = array('username'        => 'identoo.com/dirk.olbertz',
	                      'local_username'  => 'dirk.olbertz',
	                      'single_username' => 'dirk.olbertz',
	                      'namespace'       => '',
	                      'servername'      => 'identoo.com',
	                      'local'           => 0);
	                      
	    $result = $this->model->splitUsername('identoo.com/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	function testSplitUsernameExternPath() {
	    $expected = array('username'        => 'identoo.com/noserub/dirk.olbertz',
	                      'local_username'  => 'dirk.olbertz',
	                      'single_username' => 'dirk.olbertz',
	                      'namespace'       => '',
	                      'servername'      => 'identoo.com',
	                      'local'           => 0);
	                      
	    $result = $this->model->splitUsername('identoo.com/noserub/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	function testSplitUsernameWithHttp() {
	    $server_base = str_replace('http://', '', FULL_BASE_URL . Router::url('/'));
	    $server_base = str_replace('https://', '', $server_base);
	    $server_base = trim($server_base, '/');
	    
	    $expected = array('username'        => $server_base . '/dirk.olbertz',
	                      'local_username'  => 'dirk.olbertz',
	                      'single_username' => 'dirk.olbertz',
	                      'namespace'       => '',
	                      'servername'      => $server_base,
	                      'local'           => 1);
	                      
	    $result = $this->model->splitUsername('http://' . $server_base . '/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	function testSplitUsernameWithHttps() {
	    $server_base = str_replace('http://', '', FULL_BASE_URL . Router::url('/'));
	    $server_base = str_replace('https://', '', $server_base);
	    $server_base = trim($server_base, '/');
	    
	    $expected = array('username'        => $server_base . '/dirk.olbertz',
	                      'local_username'  => 'dirk.olbertz',
	                      'single_username' => 'dirk.olbertz',
	                      'namespace'       => '',
	                      'servername'      => $server_base,
	                      'local'           => 1);
	                      
	    $result = $this->model->splitUsername('https://' . $server_base . '/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	function testSplitUsernameWithWww() {
	    $server_base = str_replace('http://', '', FULL_BASE_URL . Router::url('/'));
	    $server_base = str_replace('https://', '', $server_base);
	    $server_base = trim($server_base, '/');
	    
	    $expected = array('username'        => $server_base . '/dirk.olbertz',
	                      'local_username'  => 'dirk.olbertz',
	                      'single_username' => 'dirk.olbertz',
	                      'namespace'       => '',
	                      'servername'      => $server_base,
	                      'local'           => 1);
	                      
	    $result = $this->model->splitUsername('www.' . $server_base . '/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	function testSplitUsernameWithHttpAndWww() {
	    $server_base = str_replace('http://', '', FULL_BASE_URL . Router::url('/'));
	    $server_base = str_replace('https://', '', $server_base);
	    $server_base = trim($server_base, '/');
	    
	    $expected = array('username'        => $server_base . '/dirk.olbertz',
	                      'local_username'  => 'dirk.olbertz',
	                      'single_username' => 'dirk.olbertz',
	                      'namespace'       => '',
	                      'servername'      => $server_base,
	                      'local'           => 1);
	                      
	    $result = $this->model->splitUsername('http://www.' . $server_base . '/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	function testSplitUsernameWithHttpsAndWww() {
	    $server_base = str_replace('http://', '', FULL_BASE_URL . Router::url('/'));
	    $server_base = str_replace('https://', '', $server_base);
	    $server_base = trim($server_base, '/');
	    
	    $expected = array('username'        => $server_base . '/dirk.olbertz',
	                      'local_username'  => 'dirk.olbertz',
	                      'single_username' => 'dirk.olbertz',
	                      'namespace'       => '',
	                      'servername'      => $server_base,
	                      'local'           => 1);
	                      
	    $result = $this->model->splitUsername('https://www.' . $server_base . '/dirk.olbertz');
	    $this->assertEqual($expected, $result);
	}
	
	# sanitizeUsername($username)
	
	function testSanitizeUsername() {
	    $username = 'test@bc';
	    $expected = 'testbc';
	    $result = $this->model->sanitizeUsername($username);
	    $this->assertEqual($expected, $result);
	}
	
	function testSanitizeUsername2() {
	    $username = 'test-bc';
	    $expected = 'test-bc';
	    $result = $this->model->sanitizeUsername($username);
	    $this->assertEqual($expected, $result);
	}
	
	function testSanitizeUsername3() {
	    $username = 'te_stbc';
	    $expected = 'te_stbc';
	    $result = $this->model->sanitizeUsername($username);
	    $this->assertEqual($expected, $result);
	}
	
	function testSanitizeUsername4() {
	    $username = 'test34bc';
	    $expected = 'test34bc';
	    $result = $this->model->sanitizeUsername($username);
	    $this->assertEqual($expected, $result);
	}
	
	function testSanitizeUsername5() {
	    $username = 'test.bc';
	    $expected = 'test.bc';
	    $result = $this->model->sanitizeUsername($username);
	    $this->assertEqual($expected, $result);
	}
	
	function testSanitizeUsername6() {
	    $username = 'te!c';
	    $expected = 'tec';
	    $result = $this->model->sanitizeUsername($username);
	    $this->assertEqual($expected, $result);
	}
	
	function testSanitizeUsername7() {
	    $username = 'Pötter';
	    $expected = 'Poetter';
	    $result = $this->model->sanitizeUsername($username);
	    $this->assertEqual($expected, $result);
	}
	
	function testSanitizeUsername8() {
	    $username = 'äöüßÄÖÜ';
	    $expected = 'aeoeuessAeOeUe';
	    $result = $this->model->sanitizeUsername($username);
	    $this->assertEqual($expected, $result);
	}
	
	function testSanitizeUsername9() {
	    $username = 'kein leerzeichen erlaubt';
	    $expected = 'kein-leerzeichen-erlaubt';
	    $result = $this->model->sanitizeUsername($username);
	    $this->assertEqual($expected, $result);
	}
	
	function tearDown() {
	    unset($this->model);
	}
}
?>