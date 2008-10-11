<?php

class OmbRequestToken extends AppModel {

	public function authorize($token_key, $identity_id) {
		$this->updateAll(array('authorized' => true, 'identity_id' => $identity_id), array('OmbRequestToken.token_key' => $token_key));
	}
}