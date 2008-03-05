<?php
class DailymotionService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#dailymotion.com/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.dailymotion.com/'.$username.'/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://www.dailymotion.com/contacts/' . $username, '/<img width="80" height="80" src=".*" alt="(.*)" \/>/simU', '/next&nbsp;&raquo;<\/a>/iU', '/');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://www.dailymotion.com/rss/'.$username;
	}
}
?>