<?php
class LivejournalService extends AbstractService {
	
	public function init() {
	    $this->name = 'LiveJournal';
        $this->url = 'http://www.livejournal.com/';
        $this->service_type_id = 3;
        $this->icon = 'livejorunal.gif';
        $this->has_feed = true;
	}
	
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