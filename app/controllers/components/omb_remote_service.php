<?php

$pathExtra = APP.DS.'vendors'.DS.PATH_SEPARATOR.APP.DS.'vendors'.DS.'pear'.DS.PATH_SEPARATOR.VENDORS.PATH_SEPARATOR.VENDORS.'pear';
$path = ini_get('include_path');
$path = $pathExtra . PATH_SEPARATOR . $path;
ini_set('include_path', $path);

// avoids a "cannot redeclare" error when using this component in the Entry model
if (!class_exists('Auth_Yadis_Yadis')) {
	App::import('Vendor', 'yadis', array('file' => 'Auth'.DS.'Yadis'.DS.'Yadis.php'));
}
App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth.php'));
App::import('Vendor', array('OauthConstants', 'OmbConstants', 'OmbParamKeys'));

class OmbRemoteServiceComponent extends Object {
	public $components = array('OmbOauthConsumer', 'Session');
	private $controller = null;
	private $services = null;
	
	public function __construct() {
		$this->services = array('http://oauth.net/discovery/1.0',
								OauthConstants::REQUEST,
						  		OauthConstants::AUTHORIZE,
						  		OauthConstants::ACCESS,
								OmbConstants::VERSION,
						  		OmbConstants::POST_NOTICE,
						  		OmbConstants::UPDATE_PROFILE);
	}
	
	public function startUp($controller) {
		$this->controller = $controller;
	}
	
	public function discoverLocalService($url) {
		App::import('Vendor', array('OmbLocalServiceDefinition', 'UrlUtil'));
		$url = UrlUtil::addHttpIfNoProtocolSpecified($url);
		$xrds = $this->discoverXRDS($url);
		$yadisServices = $xrds->services(array(array($this, 'filterServices')));
		
		// TODO refactor this code to make it more readable
		foreach ($yadisServices as $yadisService) {
			$types = $yadisService->getTypes();
			$uris = $yadisService->getURIs();
			
			if ($types && $uris) {
				foreach ($uris as $uri) {
					$xrd = $this->getXrd($uri, $xrds);
					$ends = $xrd->services(array(array($this, 'filterServices')));
					
					foreach ($ends as $end) {
						$typ = $end->getTypes();
						$e = '';
						
						foreach ($typ as $t) {
							if (in_array($t, $this->services)) {
								$e = $t;
							}
							if ($t == OauthConstants::REQUEST) {
								$data = $end->getElements('xrd:LocalID');
								$localID = $end->parser->content($data[0]);
							}
						}
						$request = $end->getURIs();
						$endpoints[$e] = $request[0];
					}
				}
			}
		}
		
		return new OmbLocalServiceDefinition($localID, $endpoints);
	}
	
	// internal callback method, don't use it outside this class
	public function filterServices($service) {
		$uris = $service->getTypes();
		
		foreach ($uris as $uri) {
			if (in_array($uri, $this->services)) {
				return true;
			}
		}
		
		return false;
	}
	
	public function getAccessToken($accessTokenUrl, $requestToken) {
		return $this->OmbOauthConsumer->getAccessToken('GenericOmb', $accessTokenUrl, $requestToken);
	}
	
	public function getRequestToken($requestTokenUrl, $localId) {
		return $this->OmbOauthConsumer->getRequestToken('GenericOmb', 
													 	$requestTokenUrl, 
													 	'POST', 
													 	array(OmbParamKeys::VERSION => OmbConstants::VERSION, 
															  OmbParamKeys::LISTENER => $localId));
	}
	
	public function postNotice($tokenKey, $tokenSecret, $url, $noticeId, $notice) {
		$identity = $this->Session->read('Identity');
		$data = $this->OmbOauthConsumer->post('GenericOmb', 
											  $tokenKey, 
											  $tokenSecret, 
											  $url, 
											  array(OmbParamKeys::VERSION => OmbConstants::VERSION, 
													OmbParamKeys::LISTENEE => 'http://'.$identity['username'], 
													OmbParamKeys::NOTICE => Router::url('/entry/'.$noticeId, true), 
													OmbParamKeys::NOTICE_CONTENT => $notice));
		return $data;
	}
	
	public function redirectToAuthorizationPage($authorizeUrl, $requestToken, OmbAuthorizationParams $ombAuthorizationParams) {
		$authUrl = $this->removeQueryStringIfLaconica($authorizeUrl);
		$consumer = $this->getConsumer();
		$request = OAuthRequest::from_consumer_and_token($consumer, $requestToken, 'GET', $authUrl, array());

		$params = $ombAuthorizationParams->getAsArray();
		foreach ($params as $key => $value) {
			$request->set_parameter($key, $value);
		}

		$request->set_parameter('oauth_callback', Configure::read('NoseRub.full_base_url') . $params[OmbParamKeys::LISTENEE_NICKNAME].'/callback');
		
		if ($this->isLaconica($authorizeUrl)) {
			// adding querystring param we removed above
			$request->set_parameter('action', 'userauthorization');
		}
		
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $requestToken);
		
		$this->controller->redirect($request->to_url());
	}
	
	public function updateProfile($tokenKey, $tokenSecret, $url, OmbUpdatedProfileData $profileData) {
		$identity = $this->Session->read('Identity');
		
		$result = false;
		$profileDataAsArray = $profileData->getAsArray();
		
		if ($profileDataAsArray) {
			$result = $this->OmbOauthConsumer->post('GenericOmb',
													$tokenKey,
													$tokenSecret,
													$url,
													array_merge(array(OmbParamKeys::VERSION => OmbConstants::VERSION,
												  					  OmbParamKeys::LISTENEE => 'http://'.$identity['username']),
												  				$profileDataAsArray));
		}
		
		return $result;
	}
	
	private function discoverXRDS($url) {
		$fetcher = Auth_Yadis_Yadis::getHTTPFetcher();
		$yadis = Auth_Yadis_Yadis::discover($url, $fetcher);
		
		if (!$yadis || $yadis->isFailure()) {
			throw new Exception('Yadis document not found');
		}
		
		$xrds = Auth_Yadis_XRDS::parseXRDS($yadis->response_text);
		
		if (!$xrds) {
			throw new Exception('XRDS data not found');
		}
		
		return $xrds;
	}
	
	private function getConsumer() {
		App::import('File', COMPONENTS.'oauth_consumers'.DS.'generic_omb_consumer.php');
		$ombConsumer = new GenericOmbConsumer();
		
		return $ombConsumer->getConsumer();
	}
	
	private function getXrd($uri, $xrds) {
		if (strpos($uri, '#') !== 0) {
			return;
		}
		
		$xmlID = substr($uri, 1);
		$nodes = $xrds->allXrdNodes;
		$parser = $xrds->parser;
		
		foreach ($nodes as $node) {
			$attributes = $parser->attributes($node);
			
			if (isset($attributes['xml:id']) && $attributes['xml:id'] == $xmlID) {
				$node = array($node);
				return new Auth_Yadis_XRDS($parser, $node);
			}
		}
	}
	
	private function isLaconica($authorizeUrl) {
		if (strpos($authorizeUrl, '?') === false) {
			return false;
		}
		
		return true;
	}
	
	// laconica uses urls like http://example.com/index.php?action=userauthorization 
	// which the OAuth library doesn't like, so we have to remove the querystring
	private function removeQueryStringIfLaconica($authorizeUrl) {
		if ($this->isLaconica($authorizeUrl)) {
			$authUrl = explode('?', $authorizeUrl);
			$authUrl = $authUrl[0];
		} else {
			$authUrl = $authorizeUrl;
		}
		
		return $authUrl;
	}
}