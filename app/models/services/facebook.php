<?php
class FacebookService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#facebook.com/profile.php\?id=(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.facebook.com/profile.php?id='.$username;
	}
}
?>