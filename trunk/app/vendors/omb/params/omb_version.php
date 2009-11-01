<?php
App::import('Vendor', array('OmbConstants', 'OmbParam', 'OmbParamKeys'));

class OmbVersion extends OmbParam {
	public function __construct() {
		parent::__construct(OmbConstants::VERSION);
	}
	
	public function getKey() {
		return OmbParamKeys::VERSION;
	}
}