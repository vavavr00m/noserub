<?php
class SnipplrService extends AbstractService {
	
	public function init() {
	    $this->name = 'Snipplr';
        $this->url = 'http://snipplr.com/';
        $this->service_type_id = 5;
        $this->icon = 'snipplr.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#snipplr.com/users/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://snipplr.com/users/' . $username . '/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
	    return 'http://snipplr.com/rss/users/' . $username;
	}
}