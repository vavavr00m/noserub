<?php
class ViddyouService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#viddyou.com/profile.php\?user=(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://viddyou.com/profile.php?user='.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://viddyou.com/profile.php?user=' . $username . '/friends/', '/next>/iU');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://www.viddyou.com/feed/user/'.$username.'/feed.rss';
	}
}
?>