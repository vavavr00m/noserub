<?php
class UpcomingService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#upcoming.yahoo.com/user/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://upcoming.yahoo.com/user/'.$username.'/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://upcoming.yahoo.com/user/' . $username . '/', '/<a href="\/user\/[0-9]*\/">(.*)<\/a>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://upcoming.yahoo.com/syndicate/v2/my_events/'.$username;
	}
}
?>