<?php
class JaikuService extends AbstractService {
	
	public function init() {
	    $this->name = 'Jaiku';
        $this->url = 'http://jaiku.com/';
        $this->service_type = 5;
        $this->icon = 'jaiku.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).jaiku.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.jaiku.com';
	}
	
    public function getFeedUrl($username) {
	    return 'http://'.$username.'.jaiku.com/feed/rss';
	}
}