<?php
App::import('Vendor', 'OmbListeneeNickname');

class OmbListeneeNicknameTest extends CakeTestCase {
	public function testConstruct() {
		$nickname = 'nick';
		$listeneeNickname = new OmbListeneeNickname($nickname);
		$this->assertEqual(OmbParamKeys::LISTENEE_NICKNAME, $listeneeNickname->getKey());
		$this->assertEqual($nickname, $listeneeNickname->getValue());
	}
	
	public function testDotsInNicknameAreRemoved() {
		$nickname = 'nick.nick.nick';
		$nicknameWithoutDots = 'nicknicknick';
		$listeneeNickname = new OmbListeneeNickname($nickname);
		$this->assertEqual($nicknameWithoutDots, $listeneeNickname->getValue());
	}
}