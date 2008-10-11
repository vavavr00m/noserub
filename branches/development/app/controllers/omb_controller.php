<?php

App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth.php'));

class OmbController extends AppController {
	public $uses = array('OmbDataStore', 'OmbRequestToken');
	
	public function request_token() {
		Configure::write('debug', 0);
		$server = $this->getServer();
		
		// to avoid an "invalid signature" error we have to unset this param
		unset($_GET['url']);
		
		try {
			$request = OAuthRequest::from_request();
			$this->OmbDataStore->set_omb_listener($request->get_parameter('omb_listener'));
			$request_token = $server->fetch_request_token($request);
		  	echo $request_token;
		} catch (OAuthException $e) {
			print($e->getMessage() . "\n<hr />\n");
		  	print_r($request);
		}
		
		exit();
	}
	
	public function access_token() {
		// TODO add implementation
	}
	
	public function authorize() {
		// TODO add implementation
	}
	
	public function post_notice() {
		// TODO add implementation
	}
	
	public function update_profile() {
		// TODO add implementation
	}
	
	private function getServer() {
		$server = new OAuthServer($this->OmbDataStore);
		$server->add_signature_method(new OAuthSignatureMethod_HMAC_SHA1());
		
		return $server;
	}
}