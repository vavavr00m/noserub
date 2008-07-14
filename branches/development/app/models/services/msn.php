<?php
class MsnService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('/^msnim:(.+)/'));
	}
	
	public function getAccountUrl($username) {
		return 'msnim:'.$username;
	}
}
?>