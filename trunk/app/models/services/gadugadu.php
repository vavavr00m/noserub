<?php
class GadugaduService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('/^gg:(.+)/'));
	}
	
	function getAccountUrl($username) {
		return 'gg:'.$username;
	}
}
?>