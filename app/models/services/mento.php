<?php
class MentoService extends AbstractService {
	
	public function init() {
	    $this->name = 'Mento';
        $this->url = 'http://www.mento.info/';
        $this->service_type = 2;
        $this->icon = 'mento.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#www.mento.info/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.mento.info/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.mento.info/feeds/public/'.$username;
	}
}