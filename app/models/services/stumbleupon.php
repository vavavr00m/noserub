<?php
class StumbleuponService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#(.+).stumbleupon.com#'));
	}
	
	function getAccountUrl($username) {
		return 'http://'.$username.'.stumbleupon.com/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://' . $username . '.stumbleupon.com/friends/', '/<dt><a href="http:\/\/(.*).stumbleupon.com\/">.*<\/a><\/dt>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://www.stumbleupon.com/syndicate.php?stumbler='.$username;
	}
}
?>