<?php
class DeviantartService extends AbstractService {
	
	public function init() {
	    $this->name = 'deviantART';
        $this->url = 'http://deviantart.com/';
        $this->service_type_id = 3;
        $this->icon = 'deviantart.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).deviantart.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://' . $username.' . deviantart.com';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://backend.deviantart.com/rss.xml?q=gallery%3A' . $username . '+sort%3Atime&type=deviation';
	}
}