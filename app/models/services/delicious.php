<?php
class DeliciousService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#del.icio.us/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://del.icio.us/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://feeds.delicious.com/rss/'.$username;
	}
}