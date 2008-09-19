<?php
class LivejournalService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).livejournal.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.livejournal.com/';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://www.livejournal.com/tools/friendlist.bml?user=' . $username, '/lj:user=\'(.*)\'/iU', '/&gt;&gt;<\/b><\/a>/iU', '&page=');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://'.$username.'.livejournal.com/data/rss';
	}
}