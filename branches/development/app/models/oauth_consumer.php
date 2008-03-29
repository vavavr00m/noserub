<?php

class OauthConsumer extends AppModel {
	public $belongsTo = array('Identity');
	
	public function add($identity_id, $application_name) {
		$data[$this->name]['identity_id'] = $identity_id;
		$data[$this->name]['application_name'] = $application_name;
		$data[$this->name]['consumer_key'] = $this->generateConsumerKey();
		$data[$this->name]['consumer_secret'] = $this->generateConsumerSecret();
		
		return $this->save($data);
	}
	
	private function generateConsumerKey() {
		return str_replace('-', '', String::uuid());
	}
	
	private function generateConsumerSecret() {
		return md5($this->generateConsumerKey());
	}
}
?>