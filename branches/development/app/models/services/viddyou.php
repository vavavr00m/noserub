<?php
class ViddyouService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#viddyou.com/profile.php\?user=(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://viddyou.com/profile.php?user='.$username;
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://viddyou.com/profile.php?user=' . $username . '/friends/', '/next>/iU');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.viddyou.com/feed/user/'.$username.'/feed.rss';
	}
}
?>