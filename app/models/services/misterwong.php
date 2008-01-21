<?php
class MisterwongService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#mister-wong.de/user/(.+)/\?profile#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.mister-wong.de/user/'.$username.'/?profile';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.mister-wong.de/user/' . $username . '/?profile', '/<div class="username">.*<a href=".*">(.*)<\/a>/simU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://www.mister-wong.de/rss/user/'.$username.'/';
	}
}
?>