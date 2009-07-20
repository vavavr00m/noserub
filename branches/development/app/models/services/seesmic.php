<?php
class SeesmicService extends AbstractService {
	
	public function init() {
	    $this->name = 'Seesmic';
        $this->url = 'http://seesmic.com/';
        $this->service_type_id = 6;
        $this->icon = 'seesmic.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#seesmic.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://seesmic.com/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
	    return 'http://feeds.seesmic.com/user.' . $username . '.atom';
	}
}