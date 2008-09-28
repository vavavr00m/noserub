<?php
class FacebookService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#facebook.com/profile.php\?id=(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.facebook.com/profile.php?id='.$username;
	}
}