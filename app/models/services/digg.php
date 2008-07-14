<?php
class DiggService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#digg.com/users/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://digg.com/users/'.$username;
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://digg.com/users/' . $username . '/friends/view', '/<a class="fn" href="\/users\/(.*)">/iU', '/Next &#187;<\/a>/iU', '/page');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://digg.com/users/'.$username.'/history/favorites.rss';
	}
}
?>