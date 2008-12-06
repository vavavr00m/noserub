<?php
class JaikuService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).jaiku.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.jaiku.com';
	}
	
    public function getFeedUrl($username) {
	    return 'http://'.$username.'.jaiku.com/feed/rss';
	}
}