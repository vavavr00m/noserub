<?php

class OauthServiceProviderComponent extends Object {
	public function getAccessTokenKeyOrDie() {
		App::import('Vendor', 'oauth', array('file' => 'Oauth'.DS.'OAuth.php'));
		
		$server = $this->getServer();
		
		try {
			unset($_GET['url']);
			$request = OAuthRequest::from_request('GET');
			$server->verify_request($request);
		} catch (OAuthException $e) {
			print($e->getMessage());
			die();
		}
		
		return $request->get_parameter('oauth_token');
	}
	
	private function getServer() {
		App::import('Model', 'DataStore');
		$server = new OAuthServer(new DataStore());
		$server->add_signature_method(new OAuthSignatureMethod_HMAC_SHA1());
		
		return $server;
	}
}