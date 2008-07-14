<?php
class NewsvineService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).newsvine.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.newsvine.com/';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://' . $username . '.newsvine.com/?more=Friends&si=', '/<td><a href="http:\/\/(.*).newsvine.com".*>.*<\/a>/iU', '/title="Next 50">NEXT 50<\/a>/iU', '');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://'.$username.'.newsvine.com/_feeds/rss2/author';
	}
}
?>