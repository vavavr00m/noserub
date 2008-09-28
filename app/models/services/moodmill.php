<?php
class MoodmillService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#moodmill.com/citizen/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.moodmill.com/citizen/'.$username;
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.moodmill.com/citizen/' . $username, '/<div class="who">.*<a href="http:\/\/www.moodmill.com\/citizen\/(.*)\/">.*<\/a>.*<\/div>/simU');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.moodmill.com/rss/'.$username.'/';
	}
}