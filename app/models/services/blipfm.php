<?php
class BlipfmService extends AbstractService {
	
	public function init() {
	    $this->name = 'Blip.fm';
        $this->url = 'http://blip.fm/';
        $this->service_type = 7;
        $this->icon = 'blipfm.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#blip.fm/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://blip.fm/'.$username.'/';
	}
	
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://blip.fm/feed/'.$username.'';
	}
}