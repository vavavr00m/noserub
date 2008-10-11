<?php

App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth.php'));
App::import('Vendor', 'OmbConstants');

class OmbDataStore extends AppModel {
	public $useTable = false;
	private $identity_id = null;
	
	public function lookup_consumer($consumer_key) {
		return new OAuthConsumer($consumer_key, '');
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
  		
  		return new OmbOAuthToken($key, $secret);
	}
}

class OmbOAuthToken extends OAuthToken {
	public function to_string() {
		return parent::to_string() . '&omb_version=' . OAuthUtil::urlencodeRFC3986(OmbConstants::VERSION);
	}
}