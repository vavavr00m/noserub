<?php

App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth.php'));
App::import('Vendor', 'OmbConstants');

class OmbDataStore extends AppModel {
	public $useTable = false;
	private $identity_id = null;
	
	public function lookup_consumer($consumer_key) {
		return new OAuthConsumer($consumer_key, '');
	}
	
	// TODO move implementation somewhere else, as it is identical to DataStore::lookup_nonce
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
		$tokenName = 'Omb'.ucfirst($token_type).'Token';
		
		App::import('Model', $tokenName);
		$theToken = new $tokenName();
		$data = $theToken->find(array($tokenName.'.token_key' => $token));
		
		if (!empty($data)) {
			return new OAuthToken($data[$tokenName]['token_key'], $data[$tokenName]['token_secret']);
		}
		
		return null;
	}
	
	public function new_access_token($token, $consumer) {
		App::import('Model', 'OmbRequestToken');
  		$requestToken = new OmbRequestToken();
		
		if ($requestToken->isAuthorized($token->key)) {
			$this->identity_id = $requestToken->field('identity_id', array('token_key' => $token->key));
			$accessToken = $this->new_token('AccessToken');
  			$requestToken->delete(array('OmbRequestToken.token_key' => $token->key));
  			
  			return $accessToken;
		}
		
		return null;
	}
	
	public function new_request_token($consumer) {  		
  		return $this->new_token('RequestToken');
	}
	
	public function set_omb_listener($omb_listener) {
		$omb_listener = str_replace('http://', '', $omb_listener);
		
		App::import('Model', 'Identity');
		$identity = new Identity();
		
		$identity_id = $identity->field('id', array('username' => $omb_listener));
		
		$this->identity_id = $identity_id;
	}
	
	private function new_token($token_type) {
		App::import('Core', 'Security');
		$key = md5(time());
    	$secret = Security::hash(time(), null, true);
  		
    	$token_type = 'Omb'.$token_type;

    	$data[$token_type]['identity_id'] = $this->identity_id;
  		$data[$token_type]['token_key'] = $key;
  		$data[$token_type]['token_secret'] = $secret;
  		
  		App::import('Model', $token_type);
  		$token = new $token_type();
  		$token->save($data);
  		
  		if ($token_type == 'OmbRequestToken') {
  			return new OmbOAuthToken($key, $secret);
  		}
  		
  		return new OAuthToken($key, $secret);
	}
}

class OmbOAuthToken extends OAuthToken {
	public function to_string() {
		return parent::to_string() . '&omb_version=' . OAuthUtil::urlencodeRFC3986(OmbConstants::VERSION);
	}
}