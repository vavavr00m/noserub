<?php

class ApiUser extends AppModel {
	public $belongsTo = array('Identity');
	
	public $validate = array('username' => array('rule' => array('validateUniqueUsername')),
							 'password' => array('rule' => array('validateMinLengthOfPassword')));
	
	public function validateMinLengthOfPassword($value, $params = array()) {
		if (strlen($this->data['ApiUser']['password_original']) >= 6) {
			return true;
		}
		
		return false;
	}
	
	public function validateUniqueUsername($value, $params = array()) {
		$value = strtolower($value['username']);
		$id = null;
		
		if (isset($this->data['ApiUser']['id'])) {
			$id = $this->data['ApiUser']['id'];
		}
		
		return !$this->hasAny(array('ApiUser.username' => $value, 'ApiUser.id !=' => $id));
	}
	
	public function getIdentityId($username, $password) {
		return $this->field('identity_id', array('username' => $username, 
												 'password' => $this->hashPassword($password)));
	}
	
	public function saveUser($username, $password) {
		if (empty($username) && empty($password)) {
			return $this->deleteAll(array('ApiUser.identity_id' => Context::loggedInIdentityId()));
		}

		App::import('Core', 'Security');
		
		$apiUser = $this->findByIdentityId(Context::loggedInIdentityId());
		
		if ($apiUser) {
			$data['ApiUser']['id'] = $apiUser['ApiUser']['id'];
		}
		
		$data['ApiUser']['identity_id'] = Context::loggedInIdentityId();
		$data['ApiUser']['username'] = strtolower($username);
		$data['ApiUser']['password'] = $this->hashPassword($password);
		$data['ApiUser']['password_original'] = $password;
		return $this->save($data);
	}
	
	private function hashPassword($password) {
		return Security::hash($password, null, true);
	}
}