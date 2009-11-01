<?php
App::import('Vendor', array('OmbParam', 'OmbParamKeys'));

class OmbListeneeHomepage extends OmbParam {
	public function getKey() {
		return OmbParamKeys::LISTENEE_HOMEPAGE;
	}
}