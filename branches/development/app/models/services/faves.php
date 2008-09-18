<?php
class FavesService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#faves.com/users/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://faves.com/users/'.$username;
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://faves.com/FriendExplorer.aspx?user=' . $username, '/<div class="summary"><a href="http:\/\/faves.com\/users\/(.*)">/iU', '/Next<\/a>/iU', '&page=');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://faves.com/users/'.$username.'/rss';
	}
}