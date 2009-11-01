<?php

class Consumer extends AppModel {
	public $belongsTo = array('Identity');
	public $hasMany = array('AccessToken', 'RequestToken');
	
	public $validate = array('application_name' => array('rule' => 'notEmpty'),
							 'callback_url' => array('rule' => 'validateUrl', 'allowEmpty' => true));
	
	public function add($identity_id, $application_name, $callback_url) {
		$data[$this->name]['identity_id'] = $identity_id;
		$data[$this->name]['application_name'] = $application_name;
		$data[$this->name]['callback_url'] = $callback_url;
		$data[$this->name]['consumer_key'] = $this->generateConsumerKey();
		$data[$this->name]['consumer_secret'] = $this->generateConsumerSecret();
		
		return $this->save($data);
	}
	
	public function validateUrl($value, $params = array()) {
		// Cake's url validation doesn't like localhost urls, so we skip the validation if the app doesn't run in production mode
		if (Configure::read('debug') > 0) {
			return true;
		}
		
		return Validation::url($value['callback_url']);
	}
	
	private function generateConsumerKey() {
		return md5(str_replace('-', '', String::uuid()));
	}
	
	private function generateConsumerSecret() {
		App::import('Core', 'Security');
		return Security::hash($this->generateConsumerKey(), null, true);
	}
}