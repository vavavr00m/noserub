<?php
class HemidemiService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#hemidemi.com/user/(.+)/home#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.hemidemi.com/user/'.$username.'/home';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.hemidemi.com/user/' . $username . '/home/friend', '/<a href="\/user\/(.*)\/home">.*<\/a>/iU');
		
		# http://www.hemidemi.com/user/garychen67/home/friend
		# <a href="/user/kouko/home" class="user">kouko</a>
		
		
		
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.hemidemi.com/rss/user/'.$username.'/bookmark/recent.xml';
	}
}