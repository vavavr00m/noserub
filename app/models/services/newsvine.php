<?php
class NewsvineService extends AbstractService {
	
	public function init() {
	    $this->name = 'Newswine';
        $this->url = 'http://newsvine.com/';
        $this->service_type_id = 3;
        $this->icon = 'newsvine.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).newsvine.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.newsvine.com/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://'.$username.'.newsvine.com/_feeds/rss2/author';
	}
}