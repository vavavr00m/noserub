<?php
App::import('Vendor', array('OmbConstants', 'OmbParamKeys'));

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