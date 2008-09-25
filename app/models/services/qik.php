<?php
class QikService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#qik.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://qik.com/'.$username;
	}
	
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://qik.com/'.$username.'/latest-videos';
	}
}