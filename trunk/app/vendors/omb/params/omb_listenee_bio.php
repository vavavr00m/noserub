<?php
App::import('Vendor', array('OmbParam', 'OmbParamKeys'));

class OmbListeneeBio extends OmbParam {
	const MAX_LENGTH = 139; // spec says "less than 140 chars"
	
	public function __construct($bio) {
		parent::__construct($this->shortenIfTooLong($bio));
	}
	
	public function getKey() {
		return OmbParamKeys::LISTENEE_BIO;
	}
	
	private function shortenIfTooLong($bio) {
		return substr($bio, 0, self::MAX_LENGTH);
	}
}