<?php
class OdeoService extends AbstractService {
	
	public function init() {
	    $this->name = 'Odeo';
        $this->url = 'http://odeo.com/';
        $this->service_type = 7;
        $this->icon = 'odeo.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#odeo.com/profile/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://odeo.com/profile/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://odeo.com/profile/'.$username.'/rss.xml';
	}
}