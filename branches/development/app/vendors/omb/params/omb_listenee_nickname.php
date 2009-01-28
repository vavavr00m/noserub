<?php
App::import('Vendor', array('OmbParam', 'OmbParamKeys'));

class OmbListeneeNickname extends OmbParam {
	public function __construct($nickname) {
		parent::__construct($this->removeDots($nickname));
	}
	
	public function getKey() {
		return OmbParamKeys::LISTENEE_NICKNAME;
	}
	
	private function removeDots($nickname) {
		return str_replace('.', '', $nickname);
	}
}