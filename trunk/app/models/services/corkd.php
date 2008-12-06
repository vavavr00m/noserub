<?php
class CorkdService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#corkd.com/people/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://corkd.com/people/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://corkd.com/feed/journal/'.$username;
	}
}