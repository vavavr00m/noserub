<?php

class ApiUsersController extends AppController {
	public $uses = array('ApiUser');
	
	public function index() {
		if ($this->data) {
			if ($this->ApiUser->saveUser($this->data['ApiUser']['username'], $this->data['ApiUser']['password'])) {
				$this->flashMessage('success', __('The new settings have been saved.', true));
			}
		} else {
			$this->data = $this->ApiUser->findByIdentityId(Context::loggedInIdentityId());
			unset($this->data['ApiUser']['password']);
		}
	}
}