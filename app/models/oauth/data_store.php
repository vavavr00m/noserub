<?php

App::import('Vendor', 'oauth', array('file' => 'oauth'.DS.'OAuth.php'));

class DataStore extends AppModel {
	public $useTable = false;
	
	public function lookup_consumer($consumer_key) {
		$data = $this->get_consumer_data($consumer_key);
	
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
		$consumerData = $this->get_consumer_data($consumer->key);
  		
  		if (!empty($consumerData)) {
  			$key = md5(time());
    		$secret = md5(md5(time() + time()));
  			
  			$data['RequestToken']['consumer_id'] = $consumerData['Consumer']['id'];
  			$data['RequestToken']['token_key'] = $key;
  			$data['RequestToken']['token_secret'] = $secret;
  			App::import('Model', 'RequestToken');
  			$requestToken = new RequestToken();
  			$requestToken->save($data);
  			
  			return new OAuthToken($key, $secret);
  		}
  		
  		return null;
	}
	
	private function get_consumer_data($consumer_key) {
		App::import('Model', 'Consumer');
		$consumer = new Consumer();
		$consumer->recursive = -1;
		
		return $consumer->findByConsumerKey($consumer_key);
	}
}
?>