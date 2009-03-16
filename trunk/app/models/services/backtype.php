<?php
class BacktypeService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#backtype.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.backtype.com/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://feeds.backtype.com/'.$username;
	}
}