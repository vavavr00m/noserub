<?php
class WordpresscomService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).wordpress.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.wordpress.com';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://' . $username . 'wordpress.com', '/<a href="http:\/\/(.*).wordpress.com" rel=".*">.*<\/a>/iU');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://'.$username.'.wordpress.com/feed/';
	}
}