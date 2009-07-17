<?php

App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth.php'));

class DataStore extends AppModel {
	public $useTable = false;
	private $request_token_callback_url = null;
	
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
			$accessToken = new OAuthAccessToken($consumerData['Consumer']['id'], $identityId);
  			$requestToken->delete(array('RequestToken.token_key' => $token->key));
  			
  			return $accessToken;
		}
		
		return null;
	}
	
	public function new_request_token($consumer) {
		$consumerData = $this->get_consumer_data($consumer->key);
		
  		if (!empty($consumerData)) {
  			$callback_url = $consumer->callback_url;
  			
  			if ($this->request_token_callback_url != 'oob') {
  				$callback_url = $this->request_token_callback_url;
  			}
  			
  			return new OAuthRequestToken($consumerData['Consumer']['id'], $callback_url);
  		}
  		
  		return null;
	}
	
	public function set_request_token_callback_url($url) {
		$this->request_token_callback_url = $url;
	}
	
	private function get_consumer_data($consumer_key) {
		App::import('Model', 'Consumer');
		$consumer = new Consumer();
		$consumer->recursive = -1;
		
		return $consumer->findByConsumerKey($consumer_key);
	}
}

abstract class AbstractOAuthToken extends OAuthToken {	
	public function __construct() {
		App::import('Core', 'Security');
		$key = md5(time());
    	$secret = Security::hash(time(), null, true);
    	parent::__construct($key, $secret);
	}
}

class OAuthAccessToken extends AbstractOAuthToken {
	public function __construct($consumer_id, $identity_id) {
		parent::__construct();
  		$this->save($consumer_id, $identity_id);
	}
	
	private function save($consumer_id, $identity_id) {
		$data['AccessToken']['consumer_id'] = $consumer_id;
  		$data['AccessToken']['token_key'] = $this->key;
  		$data['AccessToken']['token_secret'] = $this->secret;
  		$data['AccessToken']['identity_id'] = $identity_id;
		
  		App::import('Model', 'AccessToken');
  		$token = new AccessToken();
  		$token->save($data);
	}
}

class OAuthRequestToken extends AbstractOAuthToken {
	public function __construct($consumer_id, $callback_url) {
		parent::__construct();
		$this->save($consumer_id, $callback_url);
	}
	
	private function save($consumer_id, $callback_url) {
		$data['RequestToken']['consumer_id'] = $consumer_id;
  		$data['RequestToken']['token_key'] = $this->key;
  		$data['RequestToken']['token_secret'] = $this->secret;
  		$data['RequestToken']['callback_url'] = $callback_url;
		
  		App::import('Model', 'RequestToken');
  		$token = new RequestToken();
  		$token->save($data);
	}
	
	public function to_string() {
		return parent::to_string() . '&oauth_callback_confirmed=true';
	}
}