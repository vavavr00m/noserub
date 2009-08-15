<?php
/* Part of the twitter-compatible API */
class UsersController extends ApiAppController {
	
	public function show() {
		$user_id = $this->getUserIdParameter();
		$screen_name = $this->getScreenNameParameter();
		
		if (!$user_id && !$screen_name) {
			$this->respondWithUserNotFound();
	        return;
		}
		
		if ($screen_name) {
			$user_id = ClassRegistry::init('Identity')->username2IdentityId($this->buildUsername($screen_name));
		}
		
		$data = array();
		
		if ($user_id) {
			$data = ClassRegistry::init('Entry')->getForDisplay(array('identity_id' => $user_id), 1);
		}
		
		if ($data) {
			App::import('Vendor', 'Api.ArrayFactory');
			$this->set('data', ArrayFactory::user_with_status($data[0]));
		} else {
			$this->respondWithUserNotFound();
		}
	}
	
	private function buildUsername($screen_name) {
		App::import('Vendor', 'UrlUtil');
		return UrlUtil::removeHttpAndHttps(Context::read('network.url')).$screen_name;
	}
	
	private function getScreenNameParameter() {
		if (isset($this->params['url']['screen_name'])) {
			return $this->params['url']['screen_name'];
		}
		
		if (isset($this->params['pass'][0]) && !is_numeric($this->params['pass'][0])) {
			return $this->params['pass'][0];
		}
		
		return false;
	}
	
	private function getUserIdParameter() {
		if (isset($this->params['url']['user_id'])) {
			return $this->params['url']['user_id'];
		}
		
		if (isset($this->params['pass'][0]) && is_numeric($this->params['pass'][0])) {
			return $this->params['pass'][0];
		}
		
		return false;
	}
	
	private function respondWithUserNotFound() {
		header("HTTP/1.1 404 Not Found");
	    $this->set('data', array('hash' => array('request' => $this->params['url']['url'], 
	        									 'error' => 'Not found')));
		
	}
}