<?php
class WeventService extends AbstractService {
	
	public function init() {
	    $this->name = 'Wevent';
        $this->url = 'http://wevent.org/';
        $this->service_type = 4;
        $this->icon = 'wevent.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#wevent.org/users/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://wevent.org/users/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://wevent.org/users/'.$username.'/upcoming.rss';
	}
}