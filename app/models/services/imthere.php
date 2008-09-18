<?php
class ImthereService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#imthere.com/users/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://imthere.com/users/'.$username;
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://imthere.com/users/' . $username . '/friends', '/<h1 class="name"><a href="http:\/\/imthere.com\/users\/(.*)" class="friend">.*<\/a><\/h1>/iU', '/Next<\/a><\/li>/iU', '?page=');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://imthere.com/users/'.$username.'/events?format=rss';
	}
}