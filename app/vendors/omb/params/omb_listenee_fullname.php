<?php
App::import('Vendor', array('OmbParam', 'OmbParamKeys'));

class OmbListeneeFullname extends OmbParam {
	const MAX_LENGTH = 255;
	
	public function __construct($fullname) {
		parent::__construct($this->shortenIfTooLong(trim($fullname)));
	}
	
	public function getKey() {
		return OmbParamKeys::LISTENEE_FULLNAME;
	}
	
	private function shortenIfTooLong($fullname) {
		return substr($fullname, 0, self::MAX_LENGTH);
	}
}