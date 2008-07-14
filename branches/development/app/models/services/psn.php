<?php
class PsnService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('/^psn:(.+)/'));
	}
	
	public function getAccountUrl($username) {
		return 'psn:' . $username;
	}
}
?>