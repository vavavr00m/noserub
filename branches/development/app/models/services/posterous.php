<?php
class PosterousService extends AbstractService {
	
	public function init() {
	    $this->name = 'Posterous';
        $this->url = 'http://posterous.com/';
        $this->service_type_id = 3;
        $this->icon = 'posterous.png';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).posterous.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.posterous.com/';
	}
	
	public function getTitle($feeditem) {
		return $feeditem->get_title();
	}
	
	public function getFeedUrl($username) {
	    return 'http://'.$username.'.posterous.com/rss.xml';
	}
}