<?php

class Nonce extends AppModel {

	public function add($consumer, $token, $nonce) {
		$data['Nonce']['consumer'] = $consumer->key;
		$data['Nonce']['token'] = $token->key;
		$data['Nonce']['nonce'] = $nonce;
		
		$this->create();
		$this->save($data);
	}
	
	public function deleteExpired() {
		$this->deleteAll(array('Nonce.created <= DATE_SUB(NOW(), INTERVAL 30 MINUTE)'), false);
	}
	
	public function hasBeenUsed($consumer, $token, $nonce) {
		return $this->hasAny(array('consumer' => $consumer->key, 'token' => $token->key,
									   'nonce' => $nonce));
	}
}
