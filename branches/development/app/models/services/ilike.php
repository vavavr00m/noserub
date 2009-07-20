<?php
class IlikeService extends AbstractService {
	
	public function init() {
	    $this->name = 'iLike';
        $this->url = 'http://ilike.com/';
        $this->service_type_id = 7;
        $this->icon = 'ilike.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#ilike.com/user/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://ilike.com/user/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://ilike.com/user/'.$username.'/recently_played.rss';
	}
}