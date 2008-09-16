<?php

class Nonce extends AppModel {
	public $belongsTo = array('Consumer');
		
	public function add($consumer, $token, $nonce) {
		$data['Nonce']['consumer_id'] = $this->getConsumerId($consumer);
		$data['Nonce']['token'] = $token->key;
		$data['Nonce']['nonce'] = $nonce;
		
		$this->create();
		$this->save($data);
	}
	
	public function hasBeenUsed($consumer, $token, $nonce) {
		$consumerId = $this->getConsumerId($consumer);
		
		if ($consumerId) {
			return $this->hasAny(array('consumer_id' => $consumerId, 'token' => $token->key,
									   'nonce' => $nonce));
		}
		
		return false;
	}
	
	private function getConsumerId($consumer) {
		return $this->Consumer->field('id', array('consumer_key' => $consumer->key));
	}
}
