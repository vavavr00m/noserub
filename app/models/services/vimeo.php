<?php
class VimeoService extends AbstractService {
	
	public function init() {
	    $this->name = 'Vimeo';
        $this->url = 'http://vimeo.com/';
        $this->service_type_id = 6;
        $this->icon = 'vimeo.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#vimeo.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://vimeo.com/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://vimeo.com/'.$username.'/videos/rss/';
	}
}