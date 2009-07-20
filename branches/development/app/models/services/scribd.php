<?php
class ScribdService extends AbstractService {
	
	public function init() {
	    $this->name = 'Scribd';
        $this->url = 'http://www.scribd.com/';
        $this->service_type_id = 8;
        $this->icon = 'scribd.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#scribd.com/people/view/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.scribd.com/people/view/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.scribd.com/feeds/user_rss/'.$username;
	}
}