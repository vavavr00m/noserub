<?php
class SimpyService extends AbstractService {
	
	public function init() {
	    $this->name = 'Simpy';
        $this->url = 'http://www.simpy.com/';
        $this->service_type = 2;
        $this->icon = 'simpy.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#simpy.com/user/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.simpy.com/user/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.simpy.com/rss/user/'.$username.'/links/';
	}
}