<?php
class FavesService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#faves.com/users/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://faves.com/users/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://faves.com/FriendExplorer.aspx?user=' . $username, '/<div class="summary"><a href="http:\/\/faves.com\/users\/(.*)">/iU', '/Next<\/a>/iU', '&page=');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://faves.com/users/'.$username.'/rss';
	}
}
?>