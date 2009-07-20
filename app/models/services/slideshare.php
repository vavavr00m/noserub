<?php
class SlideshareService extends AbstractService {
	
	public function init() {
	    $this->name = 'SlideShare';
        $this->url = 'http://www.slideshare.net';
        $this->service_type_id = 8;
        $this->icon = 'slideshare.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#slideshare.net/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.slideshare.net/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.slideshare.net/rss/user/'.$username;
	}
}