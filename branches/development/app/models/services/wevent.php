<?php
class WeventService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#wevent.org/users/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://wevent.org/users/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://wevent.org/users/' . $username, '/<a href="\/users\/(.*)" class="fn url" rel="friend">.*<\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://wevent.org/users/'.$username.'/upcoming.rss';
	}
}
?>