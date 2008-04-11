<?php
class WiiService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('/^wii:(.+)/'));
	}
	
	function getAccountUrl($username) {
		return 'wii:' . $username;
	}
}
?>