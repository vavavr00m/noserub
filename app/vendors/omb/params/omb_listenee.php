<?php
App::import('Vendor', array('OmbParam', 'OmbParamKeys'));

class OmbListenee extends OmbParam {
	public function getKey() {
		return OmbParamKeys::LISTENEE;
	}
}