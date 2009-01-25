<?php
App::import('Vendor', array('OmbParam', 'OmbParamKeys'));

class OmbListeneeLicense extends OmbParam {
	const CREATIVE_COMMONS = 'http://creativecommons.org/licenses/by/3.0/';
	
	public function __construct() {
		parent::__construct(self::CREATIVE_COMMONS);
	}
	
	public function getKey() {
		return OmbParamKeys::LISTENEE_LICENSE;
	}
}