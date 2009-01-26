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
	
	public function deleteByContactId($contact_id) {
		$this->deleteAll(array('contact_id' => $contact_id));
	}
	
	public function getAccessTokens($identity_id) {
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