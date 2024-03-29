<?php

App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth.php'));

class OauthController extends AppController {
	public $uses = array('DataStore', 'RequestToken');

	public function request_token() {
		Configure::write('debug', 0);
		$server = $this->getServer();
		
		// to avoid an "invalid signature" error we have to remove the url param
		$this->removeUrlParamFromQueryString();

		try {
			$request = OAuthRequest::from_request();
			$this->DataStore->set_request_token_callback_url($request->get_parameter('oauth_callback'));
			$request_token = $server->fetch_request_token($request);
		  	echo $request_token;
		} catch (OAuthException $e) {
			print($e->getMessage() . "\n<hr />\n");
		  	print_r($request);
		}
		
		exit();
	}
	
	public function access_token() {
		Configure::write('debug', 0);
		$server = $this->getServer();
		
		// we do this here so we do not have to set up a cron job
		$this->RequestToken->deleteExpired();
		
		// to avoid an "invalid signature" error we have to remove the url param
		$this->removeUrlParamFromQueryString();
		
		try {
  			$request = OAuthRequest::from_request();
  			$this->DataStore->set_verifier($request->get_parameter('oauth_verifier'));
  			$access_token = $server->fetch_access_token($request);
  			echo $access_token;
		} catch (OAuthException $e) {
  			print($e->getMessage() . "\n<hr />\n");
  			print_r($request);
		}
		
		exit();
	}
	
	public function authorize() {
		$this->writeToSessionIfParameterIsSet('OAuth.request_token', 'oauth_token');
		
		if (!$this->Session->check('Identity')) {
			$this->Session->write('Login.success_url', '/oauth/authorize');
			$this->redirect('/pages/login');
		}

		if (empty($this->params['form'])) {
			if (!$this->Session->check('OAuth.request_token')) {
				if (isset($this->params['url']['oauth_token'])) {
					$this->set('applicationName', $this->RequestToken->getApplicationName($this->params['url']['oauth_token']));
					$this->Session->write('OAuth.request_token', $this->params['url']['oauth_token']);
				} else {
					$this->render('no_token');
				}
			} else {
				$this->set('applicationName', $this->RequestToken->getApplicationName($this->Session->read('OAuth.request_token')));
			}
			
			$this->set('headline', __('Authorize access', true));
		} else {
			if (isset($this->params['form']['allow'])) {
				$requestTokenKey = $this->Session->read('OAuth.request_token');
				$this->RequestToken->authorize($requestTokenKey, $this->Session->read('Identity.id'));
				
				$data = $this->RequestToken->getData($requestTokenKey);
				$redirectTo = $data['RequestToken']['callback_url'];
				
				if (strpos($redirectTo, '?') === false) {
					$redirectTo .= '?';
				} else {
					$redirectTo .= '&';
				}
				
				$redirectTo .= 'oauth_token='.$requestTokenKey.'&oauth_verifier='.$data['RequestToken']['verifier'];
			} else {
				$redirectTo = '/';
			}
			
			$this->Session->delete('OAuth.request_token');
			
			$this->redirect($redirectTo);
		}
	}
	
	private function getServer() {
		$server = new OAuthServer($this->DataStore);
		$server->add_signature_method(new OAuthSignatureMethod_HMAC_SHA1());
		
		return $server;
	}
	
	/**
	 * Removes the url parameter, automatically added by mod_rewrite, from $_SERVER['QUERY_STRING']
	 */
	private function removeUrlParamFromQueryString() {
		$queryString = $_SERVER['QUERY_STRING'];
		
		if (strpos($queryString, '&') === false) {
			$queryString = '';
		} else {
			$queryString = substr($queryString, strpos($queryString, '&') + 1);
		}
		
		$_SERVER['QUERY_STRING'] = $queryString;
	}
	
	private function writeToSessionIfParameterIsSet($sessionKey, $paramKey) {
		if (isset($this->params['url'][$paramKey])) {
			$this->Session->write($sessionKey, $this->params['url'][$paramKey]);
		}
	}
}