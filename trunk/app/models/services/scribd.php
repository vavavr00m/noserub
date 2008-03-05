<?php
class ScribdService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#scribd.com/people/view/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.scribd.com/people/view/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.scribd.com/people/friends/' . $username, '/<div style="font-size:16px"><a href="\/people\/view\/(.*)">.*<\/a>.*<\/div>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://www.scribd.com/feeds/user_rss/'.$username;
	}
}
?>