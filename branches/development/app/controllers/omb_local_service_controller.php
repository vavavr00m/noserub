<?php

App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth.php'));
App::import('Vendor', array('OmbConstants', 'OmbParamKeys'));

class OmbLocalServiceController extends AppController {
	public $uses = array('Entry', 'OmbAccessToken', 'OmbDataStore', 'OmbRequestToken');
	public $components = array('RequestHandler');
	
	public function request_token() {
		exit(); // disabled for release
		Configure::write('debug', 0);
		$server = $this->getServer();
		
		// to avoid an "invalid signature" error we have to unset this param
		unset($_GET['url']);
		
		try {
			$request = OAuthRequest::from_request();
			$this->OmbDataStore->set_omb_listener($request->get_parameter(OmbParamKeys::LISTENER));
			$request_token = $server->fetch_request_token($request);
		  	echo $request_token;
		} catch (OAuthException $e) {
			print($e->getMessage() . "\n<hr />\n");
		  	print_r($request);
		}
		
		exit();
	}
	
	public function access_token() {
		exit(); // disabled for release
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
		exit(); // disabled for release
		if (!$this->isCorrectOMBVersion()) {
			echo __('Invalid OMB version', true);
			exit;
		}
		
		$requiredParams = array('oauth_token', 'oauth_callback', OmbParamKeys::LISTENER, 
							    OmbParamKeys::LISTENEE, OmbParamKeys::LISTENEE_PROFILE,
							    OmbParamKeys::LISTENEE_NICKNAME, OmbParamKeys::LISTENEE_LICENSE);
		
		foreach ($requiredParams as $requiredParam) {
			if (!isset($this->params['url'][$requiredParam])) {
				echo __('Missing parameter: ', true) . $requiredParam;
				exit;
			}
		}
		
		foreach ($requiredParams as $requiredParam) {
			$this->Session->write('OMB.'.$requiredParam, $this->params['url'][$requiredParam]);
		}
		
		$optionalParams = array(OmbParamKeys::LISTENEE_FULLNAME, OmbParamKeys::LISTENEE_HOMEPAGE,
								OmbParamKeys::LISTENEE_BIO, OmbParamKeys::LISTENEE_LOCATION,
								OmbParamKeys::LISTENEE_AVATAR);
		
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
		exit(); // disabled for release
		if (!$this->Session->check('Identity') || !$this->Session->check('OMB')) {
			echo __('Invalid request', true);
			exit;
		}

		if (empty($this->params['form'])) {
			$this->set('headline', __('Authorize access', true));
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
				
				$redirectTo .= 'oauth_token='.OAuthUtil::urlencodeRFC3986($this->Session->read('OMB.oauth_token'));
				$redirectTo .= '&omb_version='.OAuthUtil::urlencodeRFC3986(OmbConstants::VERSION);
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
		exit(); // disabled for release
		if (!$this->RequestHandler->isPost() || !$this->isCorrectOMBVersion('form')) {
			header('HTTP/1.1 403 Forbidden');
			echo __('Invalid request', true);
			exit;
		}
		
		$requiredParams = array(OmbParamKeys::LISTENEE, OmbParamKeys::NOTICE, OmbParamKeys::NOTICE_CONTENT);
		
		foreach ($requiredParams as $requiredParam) {
			if (!isset($this->params['form'][$requiredParam])) {
				echo __('Missing parameter: ', true) . $requiredParam;
				exit;
			}
		}
		
		// TODO add notice
		
		echo OmbParamKeys::VERSION . '=' . OmbConstants::VERSION;
		exit;
	}
	
	public function update_profile() {
		// TODO add implementation
	}
	
	private function getServer() {
		$server = new OAuthServer($this->OmbDataStore);
		$server->add_signature_method(new OAuthSignatureMethod_HMAC_SHA1());
		
		return $server;
	}
	
	// type can be 'url' or 'form'
	private function isCorrectOMBVersion($type = 'url') {
		return (isset($this->params[$type][OmbParamKeys::VERSION]) && 
				$this->params[$type][OmbParamKeys::VERSION] == OmbConstants::VERSION);
	}
	
	private function writeToSessionIfParameterIsSet($sessionKey, $paramKey) {
		if (isset($this->params['url'][$paramKey])) {
			$this->Session->write($sessionKey, $this->params['url'][$paramKey]);
		}
	}
}