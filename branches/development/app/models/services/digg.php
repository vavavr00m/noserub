<?php
class DiggService extends AbstractService {
	
	public function init() {
	    $this->name = 'Digg';
        $this->url = 'http://digg.com/';
        $this->service_type = 5;
        $this->icon = 'digg.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#digg.com/users/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://digg.com/users/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://digg.com/users/'.$username.'/history/favorites.rss';
	}
}