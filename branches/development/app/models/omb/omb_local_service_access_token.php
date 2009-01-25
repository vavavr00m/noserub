<?php

class OmbLocalServiceAccessToken extends AppModel {
	public $belongsTo = array('OmbLocalService');
	
	public function add($contact_id, $service_id, $accessToken) {
		$data[$this->name]['contact_id'] = $contact_id;
		$data[$this->name]['omb_local_service_id'] = $service_id;
		$data[$this->name]['token_key'] = $accessToken->key;
		$data[$this->name]['token_secret'] = $accessToken->secret;

		$this->create();
		return $this->save($data); 
	}
	
	/**
	 * Warning: This method returns only one access token per "post notice url"!
	 */
	public function getAccessTokensToPostNotice($identity_id) {
		$accessTokens = $this->getAccessTokens($identity_id);
		
		$alreadyUsedLocalServices = array();
		$result = array();
		foreach ($accessTokens as $accessToken) {
			if (!in_array($accessToken['OmbLocalService']['post_notice_url'], $alreadyUsedLocalServices)) {
				$alreadyUsedLocalServices[] = $accessToken['OmbLocalService']['post_notice_url'];
				$result[] = $accessToken;
			}
		}
		
		return $result;
	}
	
	/**
	 * Warning: This method returns only one access token per "update profile url"!
	 */
	public function getAccessTokensToUpdateProfile($identity_id) {
		$accessTokens = $this->getAccessTokens($identity_id);
		
		$alreadyUsedLocalServices = array();
		$result = array();
		foreach ($accessTokens as $accessToken) {
			if (!in_array($accessToken['OmbLocalService']['update_profile_url'], $alreadyUsedLocalServices)) {
				$alreadyUsedLocalServices[] = $accessToken['OmbLocalService']['update_profile_url'];
				$result[] = $accessToken;
			}
		}
		
		return $result;
	}
	
	private function getAccessTokens($identity_id) {
		$sql = 'SELECT OmbLocalServiceAccessToken.*, OmbLocalService.* 
				FROM omb_local_service_access_tokens AS OmbLocalServiceAccessToken 
				INNER JOIN contacts c 
				ON OmbLocalServiceAccessToken.contact_id = c.id 
				INNER JOIN omb_local_services OmbLocalService 
				ON OmbLocalService.id = OmbLocalServiceAccessToken.omb_local_service_id
				WHERE c.identity_id = '.$identity_id;
		
		return $this->query($sql);
	}
}