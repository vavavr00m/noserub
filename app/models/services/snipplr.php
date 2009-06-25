<?php
class SnipplrService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#snipplr.com/users/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://snipplr.com/users/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
	    return 'http://snipplr.com/rss/users/' . $username;
	}
}