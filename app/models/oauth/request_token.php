<?php

class RequestToken extends AppModel {
	public $belongsTo = array('Consumer');
	
	public function authorize($token_key, $identity_id) {
		$this->updateAll(array('authorized' => true, 'identity_id' => $identity_id), array('RequestToken.token_key' => $token_key));
	}
	
	public function deleteExpired() {
		$this->deleteAll(array('RequestToken.modified <= DATE_SUB(NOW(), INTERVAL 24 HOUR)'));
	}
	
	public function getApplicationName($token_key) {
		$data = $this->find('first', array('conditions' => array('RequestToken.token_key' => $token_key)));
		
		return $data['Consumer']['application_name'];
	}
	
	public function getData($token_key) {
		return $this->find('first', array('conditions' => array('RequestToken.token_key' => $token_key), 'contain' => false));
	}
	
	public function isAuthorized($token_key) {
		return $this->hasAny(array('RequestToken.authorized' => true, 'RequestToken.token_key' => $token_key));
	}
}