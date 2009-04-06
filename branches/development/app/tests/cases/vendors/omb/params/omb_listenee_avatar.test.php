<?php
App::import('Vendor', 'OmbListeneeAvatar');

class OmbListeneeAvatarTest extends CakeTestCase {
	public function testConstruct() {
		$avatarName = 'myavatar';
		$avatarUrl = Context::read('network.url').'static/avatars/'.$avatarName.'-medium.jpg';
		$listeneeAvatar = new OmbListeneeAvatar($avatarName);
		$this->assertEqual(OmbParamKeys::LISTENEE_AVATAR, $listeneeAvatar->getKey());
		$this->assertEqual($avatarUrl, $listeneeAvatar->getValue());
	}
	
	public function testConstructWithEmptyAvatar() {
		$avatarName = '';
		$listeneeAvatar = new OmbListeneeAvatar($avatarName);
		$this->assertEqual($avatarName, $listeneeAvatar->getValue());
	}
	
	public function testConstructWithGravatar() {
		$gravatar = 'http://gravatar.com/avatar/xy';
		$gravatar96x96 = $gravatar . '?s=96';
		$listeneeAvatar = new OmbListeneeAvatar($gravatar);
		$this->assertEqual($gravatar96x96, $listeneeAvatar->getValue());
	}
}