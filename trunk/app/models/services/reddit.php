<?php
class RedditService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#reddit.com/user/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://reddit.com/user/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://reddit.com/user/' . $username . '/contacts/', '/<a href="\/profile\/(.*)" title=".*\'s Profile" rel="contact" id=".*">/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://reddit.com/user/'.$username.'/.rss';
	}
}
?>