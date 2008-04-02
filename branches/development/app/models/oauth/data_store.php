<?php

App::import('Vendor', 'oauth', array('file' => 'oauth'.DS.'OAuth.php'));

class DataStore extends AppModel {
	public $useTable = false;
	
	public function lookup_consumer($consumer_key) {
		App::import('Model', 'Consumer');
		$consumer = new Consumer();
		$consumer->recursive = -1;
		$data = $consumer->findByConsumerKey($consumer_key);
	
		if (!empty($data)) {
			return new OAuthConsumer($data['Consumer']['consumer_key'], $data['Consumer']['consumer_secret']);
		}
		
		return null;
	}
	
	public function lookup_nonce($consumer, $token, $nonce, $timestamp) {
		// TODO add implementation
	}
	
	public function lookup_token($consumer, $token_type, $token) {
		// TODO add implementation
	}
	
	public function new_access_token($token, $consumer) {
		// TODO add implementation
	}
	
	public function new_request_token($consumer) {
		// TODO save token data in database
		$key = md5(time());
    	$secret = time() + time();
    	$token = new OAuthToken($key, md5(md5($secret)));
    	
    	return $token;
	}
}
?>