<?php
App::import('Component', 'OmbRemoteService');
App::import('Vendor', array('OmbListenee', 'OmbListeneeHomepage'));

class OmbAuthorizationParams {
	private $params = null;
	
	public function __construct($listener, array $listenee) {
		$profileUrl = $this->getProfileUrl($listenee['Identity']['username']);
		$this->params[] = new OmbVersion();
		$this->params[] = new OmbListener($listener);
		$this->params[] = new OmbListenee($profileUrl);
		$this->params[] = new OmbListeneeProfile($profileUrl);
		$this->params[] = new OmbListeneeNickname($listenee['Identity']['local_username']);
		$this->params[] = new OmbListeneeLicense();
		$this->params[] = new OmbListeneeHomepage($profileUrl);
		$this->params[] = new OmbListeneeFullname($listenee['Identity']['name']);
		$this->params[] = new OmbListeneeBio($listenee['Identity']['about']);
		$this->params[] = new OmbListeneeLocation($listenee['Identity']['address_shown']);
		$this->params[] = new OmbListeneeAvatar($listenee['Identity']['photo']);
	}
	
	public function getAsArray() {
		$result = array();
		
		foreach ($this->params as $param) {
			$result[$param->getKey()] = $param->getValue();
		}
		
		return $result;
	}
		
	private function getProfileUrl($username) {
		return 'http://'.$username;
	}
}