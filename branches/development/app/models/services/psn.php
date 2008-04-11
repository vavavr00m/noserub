<?php
class PsnService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('/^psn:(.+)/'));
	}
	
	function getAccountUrl($username) {
		return 'psn:' . $username;
	}
}
?>