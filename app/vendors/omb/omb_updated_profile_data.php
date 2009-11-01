<?php
App::import('Vendor', array('OmbListeneeAvatar', 'OmbListeneeBio', 'OmbListeneeFullname',
							'OmbListeneeLocation'));

class OmbUpdatedProfileData {
	private $params = array();
	
	public function __construct(array $data) {
		if (isset($data['Identity']['firstname']) && isset($data['Identity']['lastname'])) {
			$fullname = $data['Identity']['firstname'] . ' ' . $data['Identity']['lastname'];
			$this->params[] = new OmbListeneeFullname($fullname);
		}
		
		if (isset($data['Identity']['about'])) {
			$this->params[] = new OmbListeneeBio($data['Identity']['about']);
		}
		
		if (isset($data['Identity']['address_shown'])) {
			$this->params[] = new OmbListeneeLocation($data['Identity']['address_shown']);
		}
		
		if (isset($data['Identity']['photo'])) {
			$this->params[] = new OmbListeneeAvatar($data['Identity']['photo']);
		}
	}
	
	public function getAsArray() {
		$result = array();
		
		foreach ($this->params as $param) {
			$result[$param->getKey()] = $param->getValue();
		}
		
		return $result;
	}
}