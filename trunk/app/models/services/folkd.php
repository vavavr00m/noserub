<?php
class FolkdService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#folkd.com/user/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.folkd.com/user/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.folkd.com/user/' . $username . '/contacts/', '/<a href="\/profile\/(.*)" title=".*\'s Profile" rel="contact" id=".*">/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://www.folkd.com/rss.php?items=15&find=all&sort=&user='.$username;
	}
}
?>