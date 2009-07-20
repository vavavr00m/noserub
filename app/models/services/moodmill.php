<?php
class MoodmillService extends AbstractService {
	
	public function init() {
	    $this->name = 'MoodMill';
        $this->url = 'http://moodmill.com/';
        $this->service_type_id = 5;
        $this->icon = 'moodmill.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#moodmill.com/citizen/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.moodmill.com/citizen/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.moodmill.com/rss/'.$username.'/';
	}
}