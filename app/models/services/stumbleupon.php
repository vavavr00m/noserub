<?php
class StumbleuponService extends AbstractService {
	
	public function init() {
	    $this->name = 'StumbleUpon';
        $this->url = 'http://stumbleupon.com/';
        $this->service_type_id = 2;
        $this->icon = 'stumpleupon.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).stumbleupon.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.stumbleupon.com/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.stumbleupon.com/syndicate.php?stumbler='.$username;
	}
}