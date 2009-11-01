<?php
App::import('Vendor', 'oauth', array('file' => 'Oauth'.DS.'OAuth.php'));

class OauthServiceProviderComponent extends Object {
	
	public function getAccessTokenKey() {
		$server = $this->getServer();
		$this->removeUrlParamFromQueryString();
		
		try {
			$request = OAuthRequest::from_request();
			$server->verify_request($request);
		} catch (OAuthException $e) {
			return false;
		}
		
		return $request->get_parameter('oauth_token');
	}
	
	/**
	 * @deprecated, use getAccessTokenKey() instead
	 */
	public function getAccessTokenKeyOrDie() {
		$server = $this->getServer();
		$this->removeUrlParamFromQueryString();
		
		try {
			$request = OAuthRequest::from_request();
			$server->verify_request($request);
		} catch (OAuthException $e) {
			header('HTTP/1.1 403 Forbidden');
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
	
	private function removeUrlParamFromQueryString() {
		$queryString = $_SERVER['QUERY_STRING'];
		
		if (strpos($queryString, '&') === false) {
			$queryString = '';
		} else {
			$queryString = substr($queryString, strpos($queryString, '&') + 1);
		}
		
		$_SERVER['QUERY_STRING'] = $queryString;
	}
}