<?php

App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth.php'));
App::import('Vendor', array('OmbConstants', 'OmbParamKeys', 'UrlUtil'));

class OmbLocalServiceController extends AppController {
	public $uses = array('Entry', 'Identity', 'OmbAccessToken', 'OmbDataStore', 'OmbRequestToken');
	public $components = array('RequestHandler');
	
	public function request_token() {
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
		if (!$this->Session->check('Identity') || !$this->Session->check('OMB')) {
			echo __('Invalid request', true);
			exit;
		}

		if (empty($this->params['form'])) {
			$this->set('headline', __('Authorize access', true));
		} else {
			if (isset($this->params['form']['allow'])) {
				$data['Identity']['is_local'] = false;
				$data['Identity']['username'] = UrlUtil::removeHttpAndHttps($this->Session->read('OMB.'.OmbParamKeys::LISTENEE_PROFILE));
				
				$existingIdentityId = $this->Identity->field('id', array('Identity.username' => $data['Identity']['username']));
			
				if (!$existingIdentityId) {
					$this->Identity->save($data, true, array('is_local', 'username'));
					$existingIdentityId = $this->Identity->id;
				}
				
				$data['OmbListeneeIdentifier']['identity_id'] = $existingIdentityId;
				$data['OmbListeneeIdentifier']['identifier'] = $this->Session->read('OMB.'.OmbParamKeys::LISTENEE);
				ClassRegistry::init('OmbListeneeIdentifier')->save($data, true, array('identity_id', 'identifier'));
				
				$this->OmbRequestToken->authorize($this->Session->read('OMB.oauth_token'), $this->Session->read('Identity.id'), $existingIdentityId);

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
				$redirectTo .= '&omb_listener_location='.OAuthUtil::urlencodeRFC3986($identity['address_shown']);
				$redirectTo .= '&omb_listener_fullname='.OAuthUtil::urlencodeRFC3986($identity['name']);
				$redirectTo .= '&omb_listener_bio='.OAuthUtil::urlencodeRFC3986(substr($identity['about'], 0, 139));
				
				if ($identity['photo'] != '') {
					$redirectTo .= '&omb_listener_avatar='.OAuthUtil::urlencodeRFC3986($this->getAvatarUrl($identity['photo']));
				}
			} else {
				$redirectTo = '/';
			}
			
			$this->Session->delete('OMB');
			
			$this->redirect($redirectTo);
		}
	}
	
	public function post_notice() {
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

		$identityId = ClassRegistry::init('OmbListeneeIdentifier')->field('identity_id', array('identifier' => $this->params['form'][OmbParamKeys::LISTENEE]));
		$noticeUrl = $this->params['form'][OmbParamKeys::NOTICE];
		$notice = $this->params['form'][OmbParamKeys::NOTICE_CONTENT];

		$this->Entry->addOmbNotice($identityId, $noticeUrl, $notice);
		
		echo OmbParamKeys::VERSION . '=' . OmbConstants::VERSION;
		exit;
	}
	
	public function update_profile() {
		// TODO add implementation
	}
	
	private function getAvatarUrl($avatarName) {
		$avatarUrl = '';
		
		if (trim($avatarName) != '') {
			if ($this->isGravatarUrl($avatarName)) {
				$avatarUrl = $this->get96x96GravatarUrl($avatarName);
			} else {
				$avatarUrl = Configure::read('NoseRub.full_base_url').'static/avatars/'.$avatarName.'-medium.jpg';
			}
		}
		
		return $avatarUrl;
	}
	
	private function get96x96GravatarUrl($gravatarUrl) {
		return $gravatarUrl . '?s=96';
	}
	
	private function isGravatarUrl($avatarName) {
		return (stripos($avatarName, 'http://gravatar.com') === 0);
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