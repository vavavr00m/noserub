<?php
App::import('Vendor', array('OauthConstants', 'OmbConstants'));

class OmbLocalServiceDefinition {
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