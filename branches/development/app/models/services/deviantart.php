<?php
class DeviantartService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#(.+).deviantart.com#'));
	}
	
	function getAccountUrl($username) {
		return 'http://'.$username.'.deviantart.com';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://' . $username.'.deviantart.com/friends/', '/<a class="u" href="http:\/\/(.*).deviantart.com\/">/iU', '/Next Page<\/a>/iU', '?offset=');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://backend.deviantart.com/rss.xml?q=gallery%3A'.$username.'+sort%3Atime&type=deviation';
	}
}
?>