<?php

App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth.php'));
App::import('Vendor', 'OmbConstants');

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
		Configure::write('debug', 0);
		$server = $this->getServer();
		
		// we do this here so we do not have to set up a cron job
		$this->OmbRequestToken->deleteExpired();
		
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
		if (!$this->isCorrectOMBVersion()) {
			echo 'Invalid OMB version';
			exit;
		}
		
		$requiredParams = array('oauth_token', 'oauth_callback', 'omb_listener', 
							    'omb_listenee', 'omb_listenee_profile',
							    'omb_listenee_nickname', 'omb_listenee_license');
		
		foreach ($requiredParams as $requiredParam) {
			if (!isset($this->params['url'][$requiredParam])) {
				echo 'Missing parameter: ' . $requiredParam;
				exit;
			}
		}
		
		foreach ($requiredParams as $requiredParam) {
			$this->Session->write('OMB.'.$requiredParam, $this->params['url'][$requiredParam]);
		}
		
		$optionalParams = array('omb_listenee_fullname', 'omb_listenee_homepage',
								'omb_listenee_bio', 'omb_listenee_location',
								'omb_listenee_avatar');
		
		foreach ($optionalParams as $optionalParam) {
			$this->writeToSessionIfParameterIsSet('OMB.'.$optionalParam, $optionalParam);
		}
		
		if (!$this->Session->check('Identity')) {
			$this->Session->write('Login.success_url', '/pages/omb/authorize_form');
			$this->redirect('/pages/login');
		}
		
		$this->redirect('/pages/omb/authorize_form');
	}
	
	public function authorize_form() {
		if (!$this->Session->check('Identity') || !$this->Session->check('OMB')) {
			echo 'Invalid request';
			exit;
		}

		if (empty($this->params['form'])) {
			$this->set('headline', 'Authorize access');
		} else {
			if (isset($this->params['form']['allow'])) {
				$this->OmbRequestToken->authorize($this->Session->read('OMB.oauth_token'), $this->Session->read('Identity.id'));
				$redirectTo = $this->Session->read('OMB.oauth_callback');
				
				if (strpos($redirectTo, '?') === false) {
					$redirectTo .= '?';
				} else {
					$redirectTo .= '&';
				}
				
				$identity = $this->Session->read('Identity');
				
				$redirectTo .= 'omb_version='.OAuthUtil::urlencodeRFC3986(OmbConstants::VERSION);
				$redirectTo .= '&omb_listener_nickname='.OAuthUtil::urlencodeRFC3986($identity['local_username']);
				$redirectTo .= '&omb_listener_profile='.OAuthUtil::urlencodeRFC3986('http://'.$identity['username']);
			} else {
				$redirectTo = '/';
			}
			
			$this->Session->delete('OMB');
			
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
	
	private function isCorrectOMBVersion() {
		return (isset($this->params['url']['omb_version']) && 
				$this->params['url']['omb_version'] == OmbConstants::VERSION);
	}
	
	private function writeToSessionIfParameterIsSet($sessionKey, $paramKey) {
		if (isset($this->params['url'][$paramKey])) {
			$this->Session->write($sessionKey, $this->params['url'][$paramKey]);
		}
	}
}