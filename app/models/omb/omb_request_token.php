<?php

class OmbRequestToken extends AppModel {

	public function authorize($token_key, $identity_id, $with_identity_id) {
		$this->updateAll(array('authorized' => true, 
							   'identity_id' => $identity_id, 
							   'with_identity_id' => $with_identity_id), 
						 array('OmbRequestToken.token_key' => $token_key));
	}
	
	public function deleteExpired() {
		$this->deleteAll(array('OmbRequestToken.modified <= DATE_SUB(NOW(), INTERVAL 24 HOUR)'));
	}
	
	public function isAuthorized($token_key) {
		return $this->hasAny(array('OmbRequestToken.authorized' => true, 'OmbRequestToken.token_key' => $token_key));
	}
}