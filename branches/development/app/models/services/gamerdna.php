<?php
class GamerdnaService extends AbstractService {
	
	public function init() {
	    $this->name = 'GamerDNA';
        $this->url = 'http://www.gamerdna.com/';
        $this->service_type = 5;
        $this->icon = 'gamerdna.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).gamerdna.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.gamerdna.com';
	}
	
    public function getFeedUrl($username) {
	    return 'http://'.$username.'.gamerdna.com/rss';
	}
}