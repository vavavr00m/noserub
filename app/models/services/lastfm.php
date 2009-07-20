<?php
class LastfmService extends AbstractService {
	
	public function init() {
	    $this->name = 'Last.fm';
        $this->url = 'http://www.last.fm/';
        $this->service_type_id = 7;
        $this->icon = 'lastfm.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#last.fm/user/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.last.fm/user/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://ws.audioscrobbler.com/1.0/user/'.$username.'/recenttracks.rss';
	}
}