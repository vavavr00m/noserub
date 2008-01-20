<?php
class BloggerdeService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#(.+).blogger.de#'));
	}
	
	function getAccountUrl($username) {
		return 'http://'.$username.'.blogger.de/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://' . $username . 'blogger.de/', '/<a href="http:\/\/(.*).blogger.de" rel=".*">.*<\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://'.$username.'.blogger.de/rss?show=all';
	}
}
?>