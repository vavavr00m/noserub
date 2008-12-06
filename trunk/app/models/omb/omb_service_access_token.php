<?php

class OmbServiceAccessToken extends AppModel {
	public $belongsTo = array('OmbService');
	
	public function add($identity_id, $service_id, $accessToken) {
		$data[$this->name]['identity_id'] = $identity_id;
		$data[$this->name]['omb_service_id'] = $service_id;
		$data[$this->name]['token_key'] = $accessToken->key;
		$data[$this->name]['token_secret'] = $accessToken->secret;

		$this->create();
		return $this->save($data); 
	}
}