<?php
class OdeoService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#odeo.com/profile/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://odeo.com/profile/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://odeo.com/profile/' . $username . '/contacts/', '/<a href="\/profile\/(.*)" title=".*\'s Profile" rel="contact" id=".*">/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://odeo.com/profile/'.$username.'/rss.xml';
	}
}
?>