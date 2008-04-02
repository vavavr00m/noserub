<?php

App::import('Vendor', 'oauth', array('file' => 'oauth'.DS.'OAuth.php'));

class OauthController extends AppController {
	public $uses = array('DataStore');
	
	public function request_token() {
		Configure::write('debug', 0);
		$server = new OAuthServer($this->DataStore);
		$sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
		$server->add_signature_method($sha1_method);

		try {
			$request = OAuthRequest::from_request('POST', Router::url($this->here, true));
			$request_token = $server->fetch_request_token($request);
		  	print $request_token;
		  	exit;
		} catch (OAuthException $e) {
			print($e->getMessage() . "\n<hr />\n");
		  	print_r($request);
		  	die();
		}
	}
	
	public function access_token() {
		// TODO add implemententation
	}
	
	public function authorize() {
		// TODO add implemententation
	}
}
?>