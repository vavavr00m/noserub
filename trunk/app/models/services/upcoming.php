<?php
class UpcomingService extends AbstractService {
	
	public function init() {
	    $this->name = 'Upcoming';
        $this->url = 'http://upcoming.yahoo.com/';
        $this->service_type = 4;
        $this->icon = 'upcoming.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#upcoming.yahoo.com/user/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://upcoming.yahoo.com/user/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://upcoming.yahoo.com/syndicate/v2/my_events/'.$username;
	}
}