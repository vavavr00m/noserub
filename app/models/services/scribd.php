<?php
class ScribdService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#scribd.com/people/view/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.scribd.com/people/view/'.$username;
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.scribd.com/people/friends/' . $username, '/<div style="font-size:16px"><a href="\/people\/view\/(.*)">.*<\/a>.*<\/div>/iU');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.scribd.com/feeds/user_rss/'.$username;
	}
}