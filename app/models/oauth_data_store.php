<?php

class OauthDataStore extends AppModel {
	public $useTable = false;
	public $hasMany = array('OauthConsumer');
	
	public function lookup_consumer($consumer_key) {
		$data = $this->OauthConsumer->findByConsumerKey($consumer_key);
		
		if (!empty($data)) {
			return new OAuthConsumer($data['OauthConsumer']['consumer_key'], $data['OauthConsumer']['consumer_secret']);
		}
		
		return null;
	}
	
	public function lookup_token($consumer, $token_type, $token) {
		// TODO add implementation
	}
	
	public function new_access_token($token, $consumer) {
		// TODO add implementation
	}
	
	public function new_request_token($consumer) {
		// TODO add implementation
	}
}
?>