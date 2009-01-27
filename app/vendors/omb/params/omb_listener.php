<?php
App::import('Vendor', array('OmbParam', 'OmbParamKeys'));

class OmbListener extends OmbParam {
	public function getKey() {
		return OmbParamKeys::LISTENER;
	}
}