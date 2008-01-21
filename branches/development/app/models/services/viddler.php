<?php
class ViddlerService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#viddler.com/explore/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.viddler.com/explore/'.$username.'/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.viddler.com/explore/' . $username . '/friends/', '/<p><strong><a.*href="\/explore\/.*\/".*>(.*)<\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://www.viddler.com/explore/'.$username.'/videos/feed/';
	}
}
?>