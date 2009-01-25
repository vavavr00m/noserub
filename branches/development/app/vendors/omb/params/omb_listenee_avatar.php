<?php
App::import('Vendor', array('OmbParam', 'OmbParamKeys'));

class OmbListeneeAvatar extends OmbParam {
	
	public function __construct($avatarName) {
		$avatarUrl = '';
		
		if (trim($avatarName) != '') {
			if ($this->isGravatarUrl($avatarName)) {
				$avatarUrl = $this->get96x96GravatarUrl($avatarName);
			} else {
				$avatarUrl = Configure::read('NoseRub.full_base_url').'static/avatars/'.$avatarName.'-medium.jpg';
			}
		}
		
		parent::__construct($avatarUrl);
	}
	
	public function getKey() {
		return OmbParamKeys::LISTENEE_AVATAR;
	}
	
	private function get96x96GravatarUrl($gravatarUrl) {
		return $gravatarUrl . '?s=96';
	}
	
	private function isGravatarUrl($avatarName) {
		return (stripos($avatarName, 'http://gravatar.com') === 0);
	}
}