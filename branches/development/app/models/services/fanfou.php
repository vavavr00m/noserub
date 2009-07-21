<?php
class FanfouService extends AbstractService {
	
	public function init() {
	    $this->name = 'Fanfou';
        $this->url = 'http://fanfou.com/';
        $this->service_type = 5;
        $this->icon = 'fanfou.gif';
        $this->has_feed = true;
        $this->minutes_between_updates = 5;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#fanfou.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://fanfou.com/'.$username;
	}
	
	public function getContent($feeditem) {
		# cut off the username
		$content = $feeditem->get_title();
        return substr($content, strpos($content, ': ') + 2);
	}
	
	public function getFeedUrl($username) {
	    return 'http://api.fanfou.com/statuses/user_timeline/'.$username.'.rss';
	    #http://api.fanfou.com/statuses/user_timeline/dominik.rss
	}
}