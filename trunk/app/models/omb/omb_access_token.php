<?php

class OmbAccessToken extends AppModel {
	public $belongsTo = array('OmbService');
	
	public function add($identity_id, $service_id, $accessToken) {
		$data['OmbAccessToken']['identity_id'] = $identity_id;
		$data['OmbAccessToken']['omb_service_id'] = $service_id;
		$data['OmbAccessToken']['token_key'] = $accessToken->key;
		$data['OmbAccessToken']['token_secret'] = $accessToken->secret;

		$this->create();
		return $this->save($data); 
	}
}