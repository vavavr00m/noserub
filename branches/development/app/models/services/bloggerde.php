<?php
class BloggerdeService extends AbstractService {
	
	public function init() {
	    $this->name = 'Blogger.de';
        $this->url = 'http://www.blogger.de/';
        $this->service_type_id = 3;
        $this->icon = 'bloggerde.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).blogger.de#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.blogger.de/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://'.$username.'.blogger.de/rss?show=all';
	}
}