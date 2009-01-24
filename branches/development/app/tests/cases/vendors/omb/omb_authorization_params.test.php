<?php
App::import('Vendor', 'OmbAuthorizationParams');

class OmbAuthorizationParamsTest extends CakeTestCase {
	private $listener = 'http://example.com/user/12';
	private $nickname = 'joe';
	private $username = 'example.net/user/1';
	private $fullname = 'Joe Example';
	private $bio = 'My bio';
	private $location = 'MyCity, MyCountry';
	private $photo = 'joe_example';
	
	public function testGetAsArray() {
		$paramsArray = $this->getAuthorizationParamsArray();
		$this->assertEqual(OmbConstants::VERSION, $paramsArray[OmbParamKeys::VERSION]);
		$this->assertEqual($this->listener, $paramsArray[OmbParamKeys::LISTENER]);
		$this->assertEqual($this->getProfileUrl(), $paramsArray[OmbParamKeys::LISTENEE]);
		$this->assertEqual($this->nickname, $paramsArray[OmbParamKeys::LISTENEE_NICKNAME]);
		$this->assertEqual($this->getProfileUrl(), $paramsArray[OmbParamKeys::LISTENEE_PROFILE]);
		$this->assertEqual(OmbListeneeLicense::CREATIVE_COMMONS, $paramsArray[OmbParamKeys::LISTENEE_LICENSE]);
		$this->assertEqual($this->getProfileUrl(), $paramsArray[OmbParamKeys::LISTENEE_HOMEPAGE]);
		$this->assertEqual($this->fullname, $paramsArray[OmbParamKeys::LISTENEE_FULLNAME]);
		$this->assertEqual($this->bio, $paramsArray[OmbParamKeys::LISTENEE_BIO]);
		$this->assertEqual($this->location, $paramsArray[OmbParamKeys::LISTENEE_LOCATION]);
		$this->assertEqual($this->getPhotoUrl(), $paramsArray[OmbParamKeys::LISTENEE_AVATAR]);
	}
	
	public function testGetAsArrayWithTooLongBio() {
		$this->bio = str_repeat('a', OmbListeneeBio::MAX_LENGTH + 1);
		$paramsArray = $this->getAuthorizationParamsArray();
		$this->assertEqual(OmbListeneeBio::MAX_LENGTH, strlen($paramsArray[OmbParamKeys::LISTENEE_BIO]));
	}
	
	public function testGetAsArrayWithTooLongFullname() {
		$this->fullname = str_repeat('a', OmbListeneeFullname::MAX_LENGTH + 1);
		$paramsArray = $this->getAuthorizationParamsArray();
		$this->assertEqual(OmbListeneeFullname::MAX_LENGTH, strlen($paramsArray[OmbParamKeys::LISTENEE_FULLNAME]));
	}
	
	public function testGetAsArrayWithTooLongLocation() {
		$this->location = str_repeat('a', OmbListeneeLocation::MAX_LENGTH + 1);
		$paramsArray = $this->getAuthorizationParamsArray();
		$this->assertEqual(OmbListeneeLocation::MAX_LENGTH, strlen($paramsArray[OmbParamKeys::LISTENEE_LOCATION]));
	}
	
	public function testGetAsArrayWithoutPhoto() {
		$this->photo = '';
		$paramsArray = $this->getAuthorizationParamsArray();
		$this->assertEqual('', $paramsArray[OmbParamKeys::LISTENEE_AVATAR]);
	}
	
	public function testGetAsArrayWithGravatarAsPhoto() {
		$gravatar = 'http://gravatar.com/avatar/xy';
		$gravatar96x96 = $gravatar . '?s=96';
		$this->photo = $gravatar;
		$paramsArray = $this->getAuthorizationParamsArray();
		$this->assertEqual($gravatar96x96, $paramsArray[OmbParamKeys::LISTENEE_AVATAR]);
	}
	
	private function getAuthorizationParamsArray() {
		$params = new OmbAuthorizationParams($this->listener, $this->getListeneeData());
		
		return $params->getAsArray();
	}
	
	private function getListeneeData() {
		return array('Identity' => array('local_username' => $this->nickname,
										 'username' => $this->username,
										 'name' => $this->fullname,
										 'about' => $this->bio,
										 'address_shown' => $this->location,
										 'photo' => $this->photo));
	}
	
	private function getPhotoUrl() {
		return Configure::read('NoseRub.full_base_url').'static/avatars/'.$this->photo.'-medium.jpg';
	}
	
	private function getProfileUrl() {
		return 'http://'.$this->username;
	}
}