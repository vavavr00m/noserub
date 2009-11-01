<?php
class ViddyouService extends AbstractService {
	
	public function init() {
	    $this->name = 'Viddyou';
        $this->url = 'http://viddyou.com/';
        $this->service_type = 6;
        $this->icon = 'viddyou.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#viddyou.com/profile.php\?user=(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://viddyou.com/profile.php?user='.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.viddyou.com/feed/user/'.$username.'/feed.rss';
	}
}