<?php
class WordpresscomService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#(.+).wordpress.com#'));
	}
	
	function getAccountUrl($username) {
		return 'http://'.$username.'.wordpress.com';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://' . $username . 'wordpress.com', '/<a href="http:\/\/(.*).wordpress.com" rel=".*">.*<\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://'.$username.'.wordpress.com/feed/';
	}
}
?>