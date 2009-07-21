<?php
class twelvesecondsService extends AbstractService {
	
	public function init() {
	    $this->name = '12Seconds';
        $this->url = 'http://12seconds.tv/';
        $this->service_type = 6;
        $this->icon = '12seconds.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#12seconds.tv/channel/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://12seconds.tv/channel/'.$username;
	}
	
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://12seconds.tv/channel/'.$username.'/feed';
	}
}
