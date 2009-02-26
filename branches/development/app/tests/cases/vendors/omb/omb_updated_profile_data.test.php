<?php
App::import('Vendor', 'OmbUpdatedProfileData');

class OmbUpdatedProfileDataTest extends CakeTestCase {
	private $firstname = 'Joe';
	private $lastname = 'Doe';
	private $bio = 'My bio';
	private $location = 'MyCity, MyCountry';
	private $avatar = 'myavatar';
	
	public function testConstructWithEmptyArray() {
		$data = new OmbUpdatedProfileData(array());
		$this->assertIdentical(array(), $data->getAsArray());
	}
	
	public function testConstructWithUpdatedFullname() {
		$fullname = $this->firstname . ' ' . $this->lastname;
		$profileData = $this->createProfileData();
		$this->assertEqual($fullname, $profileData[OmbParamKeys::LISTENEE_FULLNAME]);
	}
	
	public function testConstructWithUpdatedFullnameConsistingOfFirstname() {
		$this->lastname = '';
		$fullname = $this->firstname;
		$profileData = $this->createProfileData();
		$this->assertEqual($fullname, $profileData[OmbParamKeys::LISTENEE_FULLNAME]);
	}
	
	public function testConstructWithUpdatedFullnameConsistingOfLastname() {
		$this->firstname = '';
		$fullname = $this->lastname;
		$profileData = $this->createProfileData();
		$this->assertEqual($fullname, $profileData[OmbParamKeys::LISTENEE_FULLNAME]);
	}
	
	public function testConstructWithTooLongFullname() {
		$this->firstname = str_repeat('a', OmbListeneeFullname::MAX_LENGTH + 1);
		$this->lastname = '';
		$fullname = str_repeat('a', OmbListeneeFullname::MAX_LENGTH);
		$profileData = $this->createProfileData();
		$this->assertEqual($fullname, $profileData[OmbParamKeys::LISTENEE_FULLNAME]);
	}
	
	public function testConstructWithUpdatedAvatar() {
		$avatarUrl = Configure::read('context.network.url').'static/avatars/'.$this->avatar.'-medium.jpg';
		$profileData = $this->createProfileDataWithAvatar();
		$this->assertEqual($avatarUrl, $profileData[OmbParamKeys::LISTENEE_AVATAR]);
	}
	
	public function testConstructWithEmptyAvatar() {
		$this->avatar = '';
		$profileData = $this->createProfileDataWithAvatar();
		$this->assertEqual('', $profileData[OmbParamKeys::LISTENEE_AVATAR]);
	}
	
	public function testConstructWithGravatar() {
		$gravatar = 'http://gravatar.com/avatar/xy';
		$gravatar96x96 = $gravatar . '?s=96';
		$this->avatar = $gravatar;
		$profileData = $this->createProfileDataWithAvatar();
		$this->assertEqual($gravatar96x96, $profileData[OmbParamKeys::LISTENEE_AVATAR]);
	}
	
	public function testConstructWithUpdatedBio() {
		$profileData = $this->createProfileDataWithBio();
		$this->assertEqual($this->bio, $profileData[OmbParamKeys::LISTENEE_BIO]);
	}
	
	public function testConstructWithTooLongBio() {
		$this->bio = str_repeat('a', OmbListeneeBio::MAX_LENGTH + 1);
		$expectedBio = str_repeat('a', OmbListeneeBio::MAX_LENGTH);
		$profileData = $this->createProfileDataWithBio();
		$this->assertEqual($expectedBio, $profileData[OmbParamKeys::LISTENEE_BIO]);
	}
	
	public function testConstructWithUpdatedLocation() {
		$profileData = $this->createProfileDataWithLocation();
		$this->assertEqual($this->location, $profileData[OmbParamKeys::LISTENEE_LOCATION]);
	}
	
	public function testConstructWithTooLongLocation() {
		$this->location = str_repeat('a', OmbListeneeLocation::MAX_LENGTH + 1);
		$expectedLocation = str_repeat('a', OmbListeneeLocation::MAX_LENGTH);
		$profileData = $this->createProfileDataWithLocation();
		$this->assertEqual($expectedLocation, $profileData[OmbParamKeys::LISTENEE_LOCATION]);
	}
	
	private function createProfileData() {
		$profileData = new OmbUpdatedProfileData(array('Identity' => array('firstname' => $this->firstname, 
																		   'lastname' => $this->lastname)));
		
		return $profileData->getAsArray();
	}
	
	private function createProfileDataWithAvatar() {
		$profileData = new OmbUpdatedProfileData(array('Identity' => array('photo' => $this->avatar)));
		
		return $profileData->getAsArray();
	}
	
	private function createProfileDataWithBio() {
		$profileData = new OmbUpdatedProfileData(array('Identity' => array('about' => $this->bio)));
		
		return $profileData->getAsArray();
	}
	
	private function createProfileDataWithLocation() {
		$profileData = new OmbUpdatedProfileData(array('Identity' => array('address_shown' => $this->location)));
		
		return $profileData->getAsArray();
	}
}