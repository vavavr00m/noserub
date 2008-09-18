<?php
class DailymotionService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#dailymotion.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.dailymotion.com/'.$username.'/';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://www.dailymotion.com/contacts/' . $username, '/<img width="80" height="80" src=".*" alt="(.*)" \/>/simU', '/next&nbsp;&raquo;<\/a>/iU', '/');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.dailymotion.com/rss/'.$username;
	}
}