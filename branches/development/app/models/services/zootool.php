<?php
class ZootoolService extends AbstractService {
	
	public function init() {
	    $this->name = 'Zootool';
        $this->url = 'http://zootool.com/';
        $this->service_type_id = 2;
        $this->icon = 'zootool.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#zootool.com/people/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.zootool.com/people/' . $username;
	}
		
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://zootool.com/feeds/' . $username . '/';
	}
}