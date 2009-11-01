<?php
class MisterwongService extends AbstractService {
	
	public function init() {
	    $this->name = 'Mister Wong (DE)';
        $this->url = 'http://www.mister-wong.de/';
        $this->service_type = 2;
        $this->icon = 'misterwong.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#mister-wong.de/user/(.+)/\?profile#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.mister-wong.de/user/'.$username.'/?profile';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.mister-wong.de/rss/user/'.$username.'/';
	}
}