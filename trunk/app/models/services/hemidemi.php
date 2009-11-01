<?php
class HemidemiService extends AbstractService {
	
	public function init() {
	    $this->name = 'HemiDemi.com';
        $this->url = 'http://www.hemidemi.com';
        $this->service_type = 2;
        $this->icon = 'hemidemi.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#hemidemi.com/user/(.+)/home#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.hemidemi.com/user/'.$username.'/home';
	}
		
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.hemidemi.com/rss/user/'.$username.'/bookmark/recent.xml';
	}
}