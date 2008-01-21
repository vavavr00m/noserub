<?php
class SecondlifeService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('/^#(.+)/'));
	}
	
	function getAccountUrl($username) {
		return '#'.$username;
	}	
}
?>