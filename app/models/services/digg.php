<?php
class DiggService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#digg.com/users/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://digg.com/users/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://digg.com/users/' . $username . '/friends/view', '/<a class="fn" href="\/users\/(.*)">/iU', '/Next &#187;<\/a>/iU', '/page');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://digg.com/users/'.$username.'/history/favorites.rss';
	}
}
?>