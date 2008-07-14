<?php
class PownceService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#pownce.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://pownce.com/'.$username.'/';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://pownce.com/' . $username . '/friends/', '/<div class="user-name">username: (.*)<\/div>/simU', '/Next Page &#187;<\/a>/iU', 'page/');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://pownce.com/feeds/public/'.$username.'/';
	}
}
?>