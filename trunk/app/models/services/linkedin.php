<?php
class LinkedinService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#www.linkedin.com/in/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.linkedin.com/in/'.$username;
	}
}
?>