<?php
class NewsvineService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#(.+).newsvine.com#'));
	}
	
	function getAccountUrl($username) {
		return 'http://'.$username.'.newsvine.com/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://' . $username . '.newsvine.com/?more=Friends&si=', '/<td><a href="http:\/\/(.*).newsvine.com".*>.*<\/a>/iU', '/title="Next 50">NEXT 50<\/a>/iU', '');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://'.$username.'.newsvine.com/_feeds/rss2/author';
	}
}
?>