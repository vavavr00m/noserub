<?php
class MoodmillService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#moodmill.com/citizen/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.moodmill.com/citizen/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.moodmill.com/citizen/' . $username, '/<div class="who">.*<a href="http:\/\/www.moodmill.com\/citizen\/(.*)\/">.*<\/a>.*<\/div>/simU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://www.moodmill.com/rss/'.$username.'/';
	}
}
?>