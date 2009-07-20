<?php
class BliptvService extends AbstractService {
	
	public function init() {
	    $this->name = 'Blip.TV';
        $this->url = 'http://www.blip.tv/';
        $this->service_type_id = 6;
        $this->icon = 'bliptv.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).blip.tv#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.blip.tv/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://'.$username.'.blip.tv/rss/';
	}
}