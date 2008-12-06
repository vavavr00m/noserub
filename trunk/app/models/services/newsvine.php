<?php
class NewsvineService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).newsvine.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.newsvine.com/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://'.$username.'.newsvine.com/_feeds/rss2/author';
	}
}