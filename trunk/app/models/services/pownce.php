<?php
class PownceService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#pownce.com/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://pownce.com/'.$username.'/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://pownce.com/' . $username . '/friends/', '/<div class="user-name">username: (.*)<\/div>/simU', '/Next Page &#187;<\/a>/iU', 'page/');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://pownce.com/feeds/public/'.$username.'/';
	}
}
?>