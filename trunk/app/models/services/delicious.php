<?php
class DeliciousService extends AbstractService {
	
	public function init() {
	    $this->name = 'Delicious';
        $this->url = array(
            'http://delicious.com/',
            'http://del.icio.us/'
        );
        $this->service_type = 2;
        $this->icon = 'delicious.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#del.icio.us/(.+)#', '#delicious.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://delicious.com/' . $username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://feeds.delicious.com/rss/' . $username;
	}
}