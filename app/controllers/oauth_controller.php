<?php

App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth.php'));

class OauthController extends AppController {
	public $uses = array('DataStore', 'RequestToken');
	
	public function request_token() {
		Configure::write('debug', 0);
		$server = $this->get_server();

		try {
			$request = OAuthRequest::from_request('POST', Router::url($this->here, true));
			$request_token = $server->fetch_request_token($request);
		  	echo $request_token;
		} catch (OAuthException $e) {
			print($e->getMessage() . "\n<hr />\n");
		  	print_r($request);
		}
		
		exit();
	}
	
	public function access_token() {
		exit('Not fully implemented yet');
		Configure::write('debug', 0);
		$server = $this->get_server();
		
		try {
  			$request = OAuthRequest::from_request('POST', Router::url($this->here, true));
  			$access_token = $server->fetch_access_token($request);
  			print $access_token;
  			exit;
		} catch (OAuthException $e) {
  			print($e->getMessage() . "\n<hr />\n");
  			print_r($request);
  			die();
		}
	}
	
	public function authorize() {
		if (!$this->Session->check('Identity')) {
			$this->Session->write('OAuth.request_token', $this->params['url']['oauth_token']);
			
			if (isset($this->params['url']['oauth_callback'])) {
				$this->Session->write('OAuth.callback_url', $this->params['url']['oauth_callback']);
			}
			
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
	
	private function get_server() {
		$server = new OAuthServer($this->DataStore);
		$server->add_signature_method(new OAuthSignatureMethod_HMAC_SHA1());
		
		return $server;
	}
}