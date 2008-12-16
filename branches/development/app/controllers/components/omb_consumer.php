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
	
	public function startUp($controller) {
		$this->controller = $controller;
	}
	
	public function discover($url) {
		App::import('Vendor', 'UrlUtil');
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
		
		return new OmbDiscoveredLocalService($localID, $endpoints);
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

class OmbDiscoveredLocalService {
	private $localId = null;
	private $urls = null;
	
	public function __construct($localId, array $urls) {
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

class OmbAuthorizationParams {
	const CREATIVE_COMMONS_LICENSE = 'http://creativecommons.org/licenses/by/3.0/';
	private $params = null;
	
	public function __construct($listener, array $listenee) {
		$this->params = array(OmbParamKeys::VERSION => OmbConstants::VERSION,
							  OmbParamKeys::LISTENER => $listener,
							  OmbParamKeys::LISTENEE => $this->getProfileUrl($listenee['Identity']['username']),
							  OmbParamKeys::LISTENEE_PROFILE => $this->getProfileUrl($listenee['Identity']['username']),
							  OmbParamKeys::LISTENEE_NICKNAME => $listenee['Identity']['local_username'],
							  OmbParamKeys::LISTENEE_LICENSE => self::CREATIVE_COMMONS_LICENSE,
							  OmbParamKeys::LISTENEE_HOMEPAGE => $this->getProfileUrl($listenee['Identity']['username']),
							  OmbParamKeys::LISTENEE_FULLNAME => OmbMaxLengthEnforcer::ensureFullnameLength($listenee['Identity']['name']),
							  OmbParamKeys::LISTENEE_BIO => OmbMaxLengthEnforcer::ensureBioLength($listenee['Identity']['about']),
							  OmbParamKeys::LISTENEE_LOCATION => OmbMaxLengthEnforcer::ensureLocationLength($listenee['Identity']['address_shown']),
							  OmbParamKeys::LISTENEE_AVATAR => $this->getPhotoUrl($listenee['Identity']['photo'])
							  );
	}
	
	public function getAsArray() {
		return $this->params;
	}
	
	private function getPhotoUrl($photoName) {
		if ($photoName != '') {
			if ($this->isGravatarUrl($photoName)) {
				return $this->get96x96GravatarUrl($photoName);
			}
			
			return Configure::read('NoseRub.full_base_url').'static/avatars/'.$photoName.'-medium.jpg';
		}
		
		return '';
	}
	
	private function getProfileUrl($username) {
		return 'http://'.$username;
	}
	
	private function get96x96GravatarUrl($gravatarUrl) {
		return $gravatarUrl . '?s=96';
	}
	
	private function isGravatarUrl($photoName) {
		return (stripos($photoName, 'http://gravatar.com') === 0);
	}
}

class OmbAuthorizationResponse {
	private $requiredKeys = array(OmbParamKeys::VERSION, 
								  OmbParamKeys::LISTENER_NICKNAME, 
								  OmbParamKeys::LISTENER_PROFILE);
	private $profileUrl = null;
	private $avatarUrl = null;

	public function __construct($urlParams) {
		if (empty($urlParams) || !$this->existRequiredKeys($urlParams) || !$this->validateRequiredValues($urlParams)) {
			throw new InvalidArgumentException('Invalid response');
		}
		
		$this->profileUrl = $urlParams[OmbParamKeys::LISTENER_PROFILE];
		$this->avatarUrl = $this->extractAvatarUrl($urlParams);
	}
	
	public function getAvatarUrl() {
		return $this->avatarUrl;
	}
	
	public function getProfileUrl() {
		return $this->profileUrl;
	}
	
	private function existRequiredKeys($urlParams) {
		foreach ($this->requiredKeys as $key) {
			if (!isset($urlParams[$key])) {
				return false;
			}
		}
		
		return true;
	}
	
	private function extractAvatarUrl($urlParams) {
		if (isset($urlParams[OmbParamKeys::LISTENER_AVATAR])) {
			return $urlParams[OmbParamKeys::LISTENER_AVATAR];
		}
		
		return '';
	}
	
	private function validateRequiredValues($urlParams) {
		return $urlParams[OmbParamKeys::VERSION] == OmbConstants::VERSION && 
		       trim($urlParams[OmbParamKeys::LISTENER_NICKNAME]) != '' &&
		       trim($urlParams[OmbParamKeys::LISTENER_PROFILE]) != '';
	}
}

class OmbUpdatedProfileData {
	private $params = array();
	
	public function __construct(array $data) {
		if (isset($data['Identity']['firstname']) && isset($data['Identity']['lastname'])) {
			$fullname = $data['Identity']['firstname'] . ' ' . $data['Identity']['lastname'];
			$this->params[] = new OmbListeneeFullname($fullname);
		}
		
		if (isset($data['Identity']['about'])) {
			$this->params[] = new OmbListeneeBio($data['Identity']['about']);
		}
		
		if (isset($data['Identity']['address_shown'])) {
			$this->params[] = new OmbListeneeLocation($data['Identity']['address_shown']);
		}
		
		if (isset($data['Identity']['photo'])) {
			$this->params[] = new OmbListeneeAvatar($data['Identity']['photo']);
		}
	}
	
	public function getAsArray() {
		$result = array();
		
		foreach ($this->params as $param) {
			$result[$param->getKey()] = $param->getValue();
		}
		
		return $result;
	}
}

/**
 * @deprecated, don't use this class anymore!
 */
class OmbMaxLengthEnforcer {
	public static function ensureBioLength($bio) {
		return substr($bio, 0, OmbListeneeBio::MAX_LENGTH);
	}
	
	public static function ensureFullnameLength($fullname) {
		return substr($fullname, 0, OmbListeneeFullname::MAX_LENGTH);
	}
	
	public static function ensureLocationLength($location) {
		return substr($location, 0, OmbListeneeLocation::MAX_LENGTH);
	}
}

interface OmbParam {
	public function getKey();
	public function getValue();
}

class OmbListeneeAvatar implements OmbParam {
	private $avatarUrl = '';
	
	public function __construct($avatarName) {
		if (trim($avatarName) != '') {
			if ($this->isGravatarUrl($avatarName)) {
				$this->avatarUrl = $this->get96x96GravatarUrl($avatarName);
			} else {
				$this->avatarUrl = Configure::read('NoseRub.full_base_url').'static/avatars/'.$avatarName.'-medium.jpg';
			}
		}
	}
	
	public function getKey() {
		return OmbParamKeys::LISTENEE_AVATAR;
	}
	
	public function getValue() {
		return $this->avatarUrl;
	}
	
	private function get96x96GravatarUrl($gravatarUrl) {
		return $gravatarUrl . '?s=96';
	}
	
	private function isGravatarUrl($avatarName) {
		return (stripos($avatarName, 'http://gravatar.com') === 0);
	}
}

class OmbListeneeBio implements OmbParam {
	const MAX_LENGTH = 139; // spec says "less than 140 chars"
	private $bio = null;
	
	public function __construct($bio) {
		$this->bio = $this->shortenIfTooLong($bio);
	}
	
	public function getKey() {
		return OmbParamKeys::LISTENEE_BIO;
	}
	
	public function getValue() {
		return $this->bio;
	}
	
	private function shortenIfTooLong($bio) {
		return substr($bio, 0, self::MAX_LENGTH);
	}
}

class OmbListeneeFullname implements OmbParam {
	const MAX_LENGTH = 255;
	private $fullname = null;
	
	public function __construct($fullname) {
		$this->fullname = $this->shortenIfTooLong(trim($fullname));
	}
	
	public function getKey() {
		return OmbParamKeys::LISTENEE_FULLNAME;
	}
	
	public function getValue() {
		return $this->fullname;
	}
	
	private function shortenIfTooLong($fullname) {
		return substr($fullname, 0, self::MAX_LENGTH);
	}
}

class OmbListeneeLocation implements OmbParam {
	const MAX_LENGTH = 254; // spec says "less than 255 chars"
	private $location = null;
	
	public function __construct($location) {
		$this->location = $this->shortenIfTooLong($location);
	}
	
	public function getKey() {
		return OmbParamKeys::LISTENEE_LOCATION;
	}
	
	public function getValue() {
		return $this->location;
	}
	
	private function shortenIfTooLong($location) {
		return substr($location, 0, self::MAX_LENGTH);
	}
}