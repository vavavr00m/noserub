<?php
class DeliciousService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#del.icio.us/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://del.icio.us/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://del.icio.us/network/' . $username . '/', '/<a class="uname" href="\/(.*)">.*<\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://del.icio.us/rss/'.$username;
	}
}
?>