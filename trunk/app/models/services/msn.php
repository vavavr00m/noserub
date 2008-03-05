<?php
class MsnService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('/^msnim:(.+)/'));
	}
	
	function getAccountUrl($username) {
		return 'msnim:'.$username;
	}
}
?>