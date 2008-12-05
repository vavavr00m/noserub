<?php
class DailymotionService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#dailymotion.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.dailymotion.com/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.dailymotion.com/rss/'.$username;
	}
}