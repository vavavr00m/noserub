<?php

$pathExtra = APP.DS.'vendors'.DS.PATH_SEPARATOR.APP.DS.'vendors'.DS.'pear'.DS.PATH_SEPARATOR.VENDORS.PATH_SEPARATOR.VENDORS.'pear';
$path = ini_get('include_path');
$path = $pathExtra . PATH_SEPARATOR . $path;
ini_set('include_path', $path);

App::import('Vendor', 'yadis', array('file' => 'Auth/Yadis/Yadis.php'));

define('OMB_VERSION', 'http://openmicroblogging.org/protocol/0.1');
define('OMB_POST_NOTICE', OMB_VERSION.'/postNotice');
define('OMB_UPDATE_PROFILE', OMB_VERSION.'/updateProfile');

define('OAUTH_VERSION', 'http://oauth.net/core/1.0');
define('OAUTH_REQUEST', OAUTH_VERSION.'/endpoint/request');
define('OAUTH_AUTHORIZE', OAUTH_VERSION.'/endpoint/authorize');
define('OAUTH_ACCESS', OAUTH_VERSION.'/endpoint/access');

class OmbConsumerComponent extends Object {
	private $services = null;
	
	public function __construct() {
		$this->services = array('http://oauth.net/discovery/1.0',
								OAUTH_REQUEST,
						  		OAUTH_AUTHORIZE,
						  		OAUTH_ACCESS,
								OMB_VERSION,
						  		OMB_POST_NOTICE,
						  		OMB_UPDATE_PROFILE);
	}
	
	public function discover($url) {
		$fetcher = Auth_Yadis_Yadis::getHTTPFetcher();
		$yadis = Auth_Yadis_Yadis::discover($url, $fetcher);
		
		if (!$yadis || $yadis->isFailure()) {
			throw new Exception('Yadis doc not found');
		}
		
		$xrds = Auth_Yadis_XRDS::parseXRDS($yadis->response_text);
		
		if (!$xrds) {
			throw new Exception('XRDS data not found');
		}
		
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
							if ($t == OAUTH_REQUEST) {
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
		
		return array($localID, $endpoints);
	}
	
	public function filterServices($service) {
		$uris = $service->getTypes();
		
		foreach ($uris as $uri) {
			if (in_array($uri, $this->services)) {
				return true;
			}
		}
		
		return false;
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