<?php
class CorkdService extends AbstractService {
	
	public function init() {
	    $this->name = "Cork'd";
        $this->url = 'http://corkd.com/';
        $this->service_type = 3;
        $this->icon = 'corkd.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#corkd.com/people/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://corkd.com/people/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://corkd.com/feed/journal/'.$username;
	}
}