<?php
class UpcomingService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#upcoming.yahoo.com/user/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://upcoming.yahoo.com/user/'.$username.'/';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://upcoming.yahoo.com/user/' . $username . '/', '/<a href="\/user\/[0-9]*\/">(.*)<\/a>/iU');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://upcoming.yahoo.com/syndicate/v2/my_events/'.$username;
	}
}
?>