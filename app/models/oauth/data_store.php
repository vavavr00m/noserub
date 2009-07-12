<?php

App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth.php'));

class DataStore extends AppModel {
	public $useTable = false;
	
	public function lookup_consumer($consumer_key) {
		$data = $this->get_consumer_data($consumer_key);
	
		if (!empty($data)) {
			return new OAuthConsumer($data['Consumer']['consumer_key'], $data['Consumer']['consumer_secret'], $data['Consumer']['callback_url']);
		}
		
		return null;
	}
	
	public function lookup_nonce($consumer, $token, $nonce, $timestamp) {
		App::import('Model', 'Nonce');
		$theNonce = new Nonce();

		// XXX if the API becomes popular we probably have to move this clean up to a cron job
		$theNonce->deleteExpired();
		
		if (!$theNonce->hasBeenUsed($consumer, $token, $nonce)) {
			$theNonce->add($consumer, $token, $nonce);
			return null;
		}
		
		return $nonce;
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
			$identityId = $requestToken->field('identity_id', array('token_key' => $token->key));
			$accessToken = $this->new_token($consumerData['Consumer']['id'], 'AccessToken', $identityId);
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
	
	private function new_token($consumer_id, $token_type, $identity_id = null) {
		App::import('Core', 'Security');
		$key = md5(time());
    	$secret = Security::hash(time(), null, true);
  		
  		$data[$token_type]['consumer_id'] = $consumer_id;
  		$data[$token_type]['token_key'] = $key;
  		$data[$token_type]['token_secret'] = $secret;
  		
  		if ($token_type == 'AccessToken') {
  			$data[$token_type]['identity_id'] = $identity_id;
  		}
  		
  		App::import('Model', $token_type);
  		$token = new $token_type();
  		$token->save($data);
  		
  		if ($token_type == 'AccessToken') {
  			return new OAuthToken($key, $secret);
  		} else {
  			return new OAuthRequestToken($key, $secret);
  		}
	}
}

class OAuthRequestToken extends OAuthToken {
	public function to_string() {
		return parent::to_string() . '&oauth_callback_confirmed=true';
	}
}