<?php
class ImthereService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#imthere.com/users/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://imthere.com/users/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://imthere.com/users/' . $username . '/friends', '/<h1 class="name"><a href="http:\/\/imthere.com\/users\/(.*)" class="friend">.*<\/a><\/h1>/iU', '/Next<\/a><\/li>/iU', '?page=');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://imthere.com/users/'.$username.'/events?format=rss';
	}
}
?>