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
		$this->writeToSessionIfParameterIsSet('OAuth.request_token', 'oauth_token');
		$this->writeToSessionIfParameterIsSet('OAuth.callback_url', 'oauth_callback');
		
		if (!$this->Session->check('Identity')) {
			$this->Session->write('Login.success_url', '/pages/omb/authorize');
			$this->redirect('/pages/login');
		}
		
		if (empty($this->params['form'])) {
			if (!$this->Session->check('OAuth.request_token')) {
				if (isset($this->params['url']['oauth_token'])) {
					$this->Session->write('OAuth.request_token', $this->params['url']['oauth_token']);
				} else {
					$this->render('no_token');
				}
			}
			
			$this->set('headline', 'Authorize access');
		} else {
			if (isset($this->params['form']['allow'])) {
				$this->OmbRequestToken->authorize($this->Session->read('OAuth.request_token'), $this->Session->read('Identity.id'));
				$redirectTo = $this->Session->read('OAuth.callback_url');
			} else {
				$redirectTo = '/';
			}
			
			$this->Session->delete('OAuth.request_token');
			$this->Session->delete('OAuth.callback_url');
			
			$this->redirect($redirectTo);
		}
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
	
	private function writeToSessionIfParameterIsSet($sessionKey, $paramKey) {
		if (isset($this->params['url'][$paramKey])) {
			$this->Session->write($sessionKey, $this->params['url'][$paramKey]);
		}
	}
}