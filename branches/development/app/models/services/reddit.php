<?php
class RedditService extends AbstractService {
	
	public function init() {
	    $this->name = 'Reddit';
        $this->url = 'http://reddit.com/';
        $this->service_type_id = 2;
        $this->icon = 'reddit.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#reddit.com/user/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://reddit.com/user/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://reddit.com/user/'.$username.'/.rss';
	}
}