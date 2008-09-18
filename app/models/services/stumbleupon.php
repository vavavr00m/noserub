<?php
class StumbleuponService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).stumbleupon.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.stumbleupon.com/';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://' . $username . '.stumbleupon.com/friends/', '/<dt><a href="http:\/\/(.*).stumbleupon.com\/">.*<\/a><\/dt>/iU');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.stumbleupon.com/syndicate.php?stumbler='.$username;
	}
}