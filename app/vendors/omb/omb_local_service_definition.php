<?php
App::import('Vendor', array('OauthConstants', 'OmbConstants'));

class OmbLocalServiceDefinition {
	private $localId = null;
	private $urls = null;
	
	public function __construct($localId, array $urls) {
		$this->localId = $localId;
		$this->urls = $this->validateUrls($urls);
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
	
	private function validateUrls($urls) {
		$urlKeys = array(OauthConstants::REQUEST, OauthConstants::AUTHORIZE, 
						 OauthConstants::ACCESS, OmbConstants::POST_NOTICE,
						 OmbConstants::UPDATE_PROFILE);
		
		foreach ($urlKeys as $urlKey) {
			if (!isset($urls[$urlKey])) {
				throw new Exception('Missing URL');
			}
		}
		
		foreach ($urlKeys as $urlKey) {
			if (!filter_var($urls[$urlKey], FILTER_VALIDATE_URL)) {
				throw new Exception('Invalid URL');
			}
		}
		
		return $urls;
	}
}