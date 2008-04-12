<?php

class RequestToken extends AppModel {
	public $belongsTo = array('Consumer');
	
	public function authorize($token_key) {
		$this->updateAll(array('authorized' => true), array('RequestToken.token_key' => $token_key));
	}
	
	public function isAuthorized($token_key) {
		return $this->hasAny(array('RequestToken.authorized' => true, 'RequestToken.token_key' => $token_key));
	}
}
?>