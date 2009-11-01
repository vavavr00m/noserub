<?php
class JoostService extends AbstractService {
	
	public function init() {
	    $this->name = 'Joost';
        $this->url = 'http://www.joost.com/';
        $this->service_type = 6;
        $this->icon = 'joost.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#joost.com/user/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.joost.com/user/'.$username.'/';
	}
	
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
	    return 'http://www.joost.com/api/events/get/' . $username . '?fmt=atom';
	}
}

