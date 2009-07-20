<?php
class BrightkiteService extends AbstractService {
	
	public function init() {
	    $this->name = 'Brightkite';
        $this->url = 'http://brightkite.com/';
        $this->service_type_id = 9;
        $this->icon = 'brightkite.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#brightkite.com/people/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://brightkite.com/people/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://brightkite.com/people/' . $username . '/objects.rss';
	}
}