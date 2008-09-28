<?php
class QypeService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#qype.com/people/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.qype.com/people/'.$username.'/';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.qype.com/people/' . $username . '/contacts/', '/<a href="http:\/\/www.qype.com\/people\/(.*)"><img alt="Benutzerfoto: .*" src=".*" title=".*" \/><\/a>/iU');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.qype.com/people/'.$username.'/rss';
	}
}