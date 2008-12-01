<?php
App::import('Vendor', 'UsernameUtil');

class UsernameUtilTest extends CakeTestCase {
	public function testIsReservedUsername() {
		$this->assertIdentical(true, UsernameUtil::isReservedUsername('login'));
		$this->assertIdentical(false, UsernameUtil::isReservedUsername('joe'));
	}
}