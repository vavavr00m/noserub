<?php
class OdeoService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#odeo.com/profile/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://odeo.com/profile/'.$username;
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://odeo.com/profile/' . $username . '/contacts/', '/<a href="\/profile\/(.*)" title=".*\'s Profile" rel="contact" id=".*">/iU');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://odeo.com/profile/'.$username.'/rss.xml';
	}
}