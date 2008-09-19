<?php
class LastfmService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#last.fm/user/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.last.fm/user/'.$username.'/';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.last.fm/user/' . $username . '/friends/', '/<a href="\/user\/(.*)\/" title=".*" class="nickname.*">.*<\/a>/iU');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://ws.audioscrobbler.com/1.0/user/'.$username.'/recenttracks.rss';
	}
}