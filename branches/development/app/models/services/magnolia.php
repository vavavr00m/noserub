<?php
class MagnoliaService extends AbstractService {
	
	public function init() {
	    $this->name = 'Ma.gnolia';
        $this->url = 'http://ma.gnolia.com/';
        $this->service_type_id = 2;
        $this->icon = 'magnolia.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#ma.gnolia.com/people/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://ma.gnolia.com/people/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://ma.gnolia.com/rss/full/people/'.$username.'/';
	}
}