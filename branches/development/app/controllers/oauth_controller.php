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
		exit('Not fully implemented yet');
		// TODO replace this "dummy" implementation with real implementation 
		if (empty($this->params['form'])) {
			$this->set('oauth_token', $this->params['url']['oauth_token']);
			$this->set('oauth_callback', $this->params['url']['oauth_callback']);
		} else {
			$this->RequestToken->authorize($this->params['form']['oauth_token']);
			$this->redirect($this->params['form']['oauth_callback'].'?oauth_token='.$this->params['form']['oauth_token']);
		}
	}
	
	private function get_server() {
		$server = new OAuthServer($this->DataStore);
		$server->add_signature_method(new OAuthSignatureMethod_HMAC_SHA1());
		
		return $server;
	}
}
?>