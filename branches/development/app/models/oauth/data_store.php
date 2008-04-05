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
		$tokenName = ucfirst($token_type).'Token';
		
		App::import('Model', $tokenName);
		$theToken = new $tokenName();
		$data = $theToken->find(array($tokenName.'.token_key' => $token));
		
		if (!empty($data)) {
			return new OAuthToken($data[$tokenName]['token_key'], $data[$tokenName]['token_secret']);
		}
		
		return null;
	}
	
	public function new_access_token($token, $consumer) {
		$consumerData = $this->get_consumer_data($consumer->key);
		App::import('Model', 'RequestToken');
  		$requestToken = new RequestToken();
		
		if (!empty($consumerData) && $requestToken->isAuthorized($token->key)) {
			$accessToken = $this->new_token($consumerData['Consumer']['id'], 'AccessToken');
  			$requestToken->delete(array('RequestToken.token_key' => $token->key));
  			
  			return $accessToken;
		}
		
		return null;
	}
	
	public function new_request_token($consumer) {
		$consumerData = $this->get_consumer_data($consumer->key);
  		
  		if (!empty($consumerData)) {
  			return $this->new_token($consumerData['Consumer']['id'], 'RequestToken');
  		}
  		
  		return null;
	}
	
	private function get_consumer_data($consumer_key) {
		App::import('Model', 'Consumer');
		$consumer = new Consumer();
		$consumer->recursive = -1;
		
		return $consumer->findByConsumerKey($consumer_key);
	}
	
	private function new_token($consumerId, $tokenType) {
		$key = md5(time());
    	$secret = md5(md5(time() + time()));
  		
  		$data[$tokenType]['consumer_id'] = $consumerId;
  		$data[$tokenType]['token_key'] = $key;
  		$data[$tokenType]['token_secret'] = $secret;
  		App::import('Model', $tokenType);
  		$token = new $tokenType();
  		$token->save($data);
  		
  		return new OAuthToken($key, $secret);
	}
}
?>