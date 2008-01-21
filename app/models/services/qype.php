<?php
class QypeService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#qype.com/people/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.qype.com/people/'.$username.'/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.qype.com/people/' . $username . '/contacts/', '/<a href="http:\/\/www.qype.com\/people\/(.*)"><img alt="Benutzerfoto: .*" src=".*" title=".*" \/><\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://www.qype.com/people/'.$username.'/rss';
	}
}
?>