<?php
class FacebookService extends AbstractService {
	
	public function init() {
	    $this->name = 'Facebook';
        $this->url = 'http://facebook.com/';
        $this->service_type = 5;
        $this->icon = 'facebook.gif';
        $this->has_feed = false;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#facebook.com/profile.php\?id=(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.facebook.com/profile.php?id='.$username;
	}
}