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
App::import('Vendor', 'OmbConstants');
App::import('Vendor', 'OauthConstants');

class OmbConsumerComponent extends Object {
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
	
	public function constructAuthorizeUrl($authorizeUrl, $localId, $requestToken, $identity) {
		$isLaconica = false;

		// laconica uses urls like http://example.com/index.php?action=userauthorization 
		// which the OAuth library doesn't like, so we simply remove the querystring
		if (strpos($authorizeUrl, '?')) {
			$authUrl = explode('?', $authorizeUrl);
			$authUrl = $authUrl[0];
			$isLaconica = true;
		} else {
			$authUrl = $authorizeUrl;
		}
		
		$consumer = $this->getConsumer();
		$request = OAuthRequest::from_consumer_and_token($consumer, $requestToken, 'GET', $authUrl, array());
		
		$mandatoryOmbParams = array('omb_version' => OmbConstants::VERSION, 
									'omb_listener' => $localId, 
									'omb_listenee' => Configure::read('NoseRub.full_base_url'), 
									'omb_listenee_profile' => 'http://'.$identity['Identity']['username'], 
									'omb_listenee_nickname' => $identity['Identity']['local_username'], 
									'omb_listenee_license' => 'http://creativecommons.org/licenses/by/3.0/'   
		);
		
		foreach ($mandatoryOmbParams as $k => $v) {
			$request->set_parameter($k, $v);
		}
		
		$optionalOmbParams = array('omb_listenee_homepage' => '', // empty because we don't know the user's homepage in NoseRub 
								   'omb_listenee_fullname' => $identity['Identity']['name'],
								   'omb_listenee_bio' => $identity['Identity']['about'],
								   'omb_listenee_location' => $identity['Identity']['address_shown'],
								   'omb_listenee_avatar' => Configure::read('NoseRub.full_base_url').'static/avatars/'.$identity['Identity']['photo'].'-medium.jpg'
		);
		
		foreach ($optionalOmbParams as $k => $v) {
			if ($v != '') {
				$request->set_parameter($k, $v);
			}
		}
		
		$request->set_parameter('oauth_callback', Configure::read('NoseRub.full_base_url') . $identity['Identity']['local_username'].'/callback');
		
		if ($isLaconica) {
			$request->set_parameter('action', 'userauthorization');
		}
		
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $requestToken);
		
		return $request->to_url();
	}
	
	public function discover($url) {
		$url = UrlUtil::addHttpIfNoProtocolSpecified($url);
		$xrds = $this->discoverXRDS($url);
		$yadisServices = $xrds->services(array(array($this, 'filterServices')));
		
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
		
		return new OmbEndPoint($localID, $endpoints);
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
													 	array('omb_version' => OmbConstants::VERSION, 
															  'omb_listener' => $localId));
	}
	
	public function postNotice($tokenKey, $tokenSecret, $url, $notice) {
		$data = $this->OmbOauthConsumer->post('GenericOmb', 
											  $tokenKey, 
											  $tokenSecret, 
											  $url, 
											  array('omb_version' => OmbConstants::VERSION, 
													'omb_listenee' => Configure::read('NoseRub.full_base_url'), 
													'omb_notice' => 'noserub://'.md5($notice), 
													'omb_notice_content' => $notice));
		return $data;
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
}

class OmbEndPoint {
	private $localId = null;
	private $urls = null;
	
	public function __construct($localId, $urls) {
		$this->localId = $localId;
		$this->urls = $urls;		
	}
	
	public function getAccessTokenUrl() {
		return $this->urls[OauthConstants::ACCESS];
	}
	
	public function getAuthorizeUrl() {
		return $this->urls[OauthConstants::AUTHORIZE];
	}
	
	public function getLocalId() {
		return $this->localId;
	}
	
	public function getPostNoticeUrl() {
		return $this->urls[OmbConstants::POST_NOTICE];
	}
	
	public function getRequestTokenUrl() {
		return $this->urls[OauthConstants::REQUEST];
	}
	
	public function getUpdateProfileUrl() {
		return $this->urls[OmbConstants::UPDATE_PROFILE];
	}
}