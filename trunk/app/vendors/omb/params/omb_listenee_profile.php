<?php
App::import('Vendor', array('OmbParam', 'OmbParamKeys'));

class OmbListeneeProfile extends OmbParam {
	public function getKey() {
		return OmbParamKeys::LISTENEE_PROFILE;
	}
}