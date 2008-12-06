<?php
App::import('Vendor', 'UsernameUtil');

class UsernameUtilTest extends CakeTestCase {
	public function testIsReservedUsername() {
		$this->assertIdentical(true, UsernameUtil::isReservedUsername('login'));
		$this->assertIdentical(false, UsernameUtil::isReservedUsername('joe'));
	}
	
	public function testSanitizeUsernameWithAtSign() {
	    $username = 'test@bc';
	    $expected = 'testbc';
	    $this->assertEqual($expected, UsernameUtil::sanitizeUsername($username));
	}
	
	public function testSanitizeUsernameWithHyphen() {
	    $username = 'test-bc';
	    $expected = 'test-bc';
	    $this->assertEqual($expected, UsernameUtil::sanitizeUsername($username));
	}
	
	public function testSanitizeUsernameWithUnderscore() {
	    $username = 'te_stbc';
	    $expected = 'te_stbc';
	    $this->assertEqual($expected, UsernameUtil::sanitizeUsername($username));
	}
	
	public function testSanitizeUsernameWithDigits() {
	    $username = 'test34bc';
	    $expected = 'test34bc';
	    $this->assertEqual($expected, UsernameUtil::sanitizeUsername($username));
	}
	
	public function testSanitizeUsernameWithDot() {
	    $username = 'test.bc';
	    $expected = 'test.bc';
	    $this->assertEqual($expected, UsernameUtil::sanitizeUsername($username));
	}
	
	public function testSanitizeUsernameWithExclamationMark() {
	    $username = 'te!c';
	    $expected = 'tec';
	    $this->assertEqual($expected, UsernameUtil::sanitizeUsername($username));
	}
	
	public function testSanitizeUsernameWithUmlaut() {
	    $username = 'Pötter';
	    $expected = 'Poetter';
	    $this->assertEqual($expected, UsernameUtil::sanitizeUsername($username));
	}
	
	public function testSanitizeUsernameWithUmlauts() {
	    $username = 'äöüßÄÖÜ';
	    $expected = 'aeoeuessAeOeUe';
	    $this->assertEqual($expected, UsernameUtil::sanitizeUsername($username));
	}
	
	public function testSanitizeUsernameWithSpaces() {
	    $username = 'no spaces allowed';
	    $expected = 'no-spaces-allowed';
	    $this->assertEqual($expected, UsernameUtil::sanitizeUsername($username));
	}
}