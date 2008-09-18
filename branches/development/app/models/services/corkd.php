<?php
class CorkdService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#corkd.com/people/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://corkd.com/people/'.$username.'/';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://corkd.com/people/' . $username . '/buddies', '/<dd class="username"><a href="\/people\/(.*)" rel="friend">.*<\/a><\/dd>/iU', '/Next &#8250;&#8250;<\/a>/iU', '?page=');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://corkd.com/feed/journal/'.$username;
	}
}