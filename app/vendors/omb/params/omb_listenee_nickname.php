<?php
App::import('Vendor', array('OmbParam', 'OmbParamKeys'));

class OmbListeneeNickname extends OmbParam {
	public function getKey() {
		return OmbParamKeys::LISTENEE_NICKNAME;
	}
}