<?php
class LivejournalService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#(.+).livejournal.com#'));
	}
	
	function getAccountUrl($username) {
		return 'http://'.$username.'.livejournal.com/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://www.livejournal.com/tools/friendlist.bml?user=' . $username, '/lj:user=\'(.*)\'/iU', '/&gt;&gt;<\/b><\/a>/iU', '&page=');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://'.$username.'.livejournal.com/data/rss';
	}
}
?>