<?php
App::import('Vendor', array('OmbParam', 'OmbParamKeys'));

class OmbListeneeLocation extends OmbParam {
	const MAX_LENGTH = 254; // spec says "less than 255 chars"
	
	public function __construct($location) {
		parent::__construct($this->shortenIfTooLong($location));
	}
	
	public function getKey() {
		return OmbParamKeys::LISTENEE_LOCATION;
	}
	
	private function shortenIfTooLong($location) {
		return substr($location, 0, self::MAX_LENGTH);
	}
}