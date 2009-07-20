<?php
class WordpresscomService extends AbstractService {
	
	public function init() {
	    $this->name = 'WordPress.com';
        $this->url = 'http://wordpress.com/';
        $this->service_type_id = 3;
        $this->icon = 'wordpresscom.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).wordpress.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://' . $username . '.wordpress.com';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://' . $username.  '.wordpress.com/feed/';
	}
}