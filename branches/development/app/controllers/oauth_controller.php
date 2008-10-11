<?php

App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth.php'));

class OauthController extends AppController {
	public $uses = array('DataStore', 'RequestToken');

	public function request_token() {
		Configure::write('debug', 0);
		$server = $this->getServer();
		
		// to avoid an "invalid signature" error we have to unset this param
		unset($_GET['url']);
		
		try {
			$request = OAuthRequest::from_request();
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
		
		// to avoid an "invalid signature" error we have to unset this param
		unset($_GET['url']);
		
		try {
  			$request = OAuthRequest::from_request();
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
		$this->writeToSessionIfParameterIsSet('OAuth.callback_url', 'oauth_callback');
		
		if (!$this->Session->check('Identity')) {
			$this->Session->write('Login.success_url', '/pages/oauth/authorize');
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
			
			$this->set('headline', 'Authorize access');
		} else {
			if (isset($this->params['form']['allow'])) {
				$this->RequestToken->authorize($this->Session->read('OAuth.request_token'), $this->Session->read('Identity.id'));
				
				if ($this->Session->check('OAuth.callback_url')) {
					$redirectTo = $this->Session->read('OAuth.callback_url');
				} else {
					$redirectTo = $this->RequestToken->getCallbackUrl($this->Session->read('OAuth.request_token'));
				}
			} else {
				$redirectTo = '/';
			}
			
			$this->Session->delete('OAuth.request_token');
			$this->Session->delete('OAuth.callback_url');
			
			$this->redirect($redirectTo);
		}
	}
	
	private function getServer() {
		$server = new OAuthServer($this->DataStore);
		$server->add_signature_method(new OAuthSignatureMethod_HMAC_SHA1());
		
		return $server;
	}
	
	private function writeToSessionIfParameterIsSet($sessionKey, $paramKey) {
		if (isset($this->params['url'][$paramKey])) {
			$this->Session->write($sessionKey, $this->params['url'][$paramKey]);
		}
	}
}