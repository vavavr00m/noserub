<?php
/* Part of the twitter-compatible API */
class AccountController extends ApiAppController {
	public $components = array('Responder', 'Security');
	
	public function verify_credentials() {
		$credentials = $this->Security->loginCredentials('basic');

		if ($credentials) {
			$this->loadModel('ApiUser');
			$identity_id = $this->ApiUser->getIdentityId($credentials['username'], $credentials['password']);
			
			if (!$identity_id) {
				$this->Responder->respondWithNotAuthorized();
				return;
			}
			
			$data = ClassRegistry::init('Entry')->getForDisplay(array('identity_id' => $identity_id), 1);
		
			App::import('Vendor', 'Api.ArrayFactory');
			$this->set('data', ArrayFactory::user_with_status($data[0]));
		} else {
			$this->Responder->respondWithNotAuthorized();
		}
	}
}