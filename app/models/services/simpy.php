<?php
class SimpyService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#simpy.com/user/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.simpy.com/user/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.simpy.com/rss/user/'.$username.'/links/';
	}
}