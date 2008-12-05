<?php
class LivejournalService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).livejournal.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.livejournal.com/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://'.$username.'.livejournal.com/data/rss';
	}
}