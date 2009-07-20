<?php
class FfffoundService extends AbstractService {
	
	public function init() {
	    $this->name = 'Ffffound!';
        $this->url = 'http://ffffound.com/';
        $this->service_type_id = 1;
        $this->icon = 'ffffound.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#ffffound.com/home/(.+)#'));
		#http://ffffound.com/home/lancew/found/
	}
	
	public function getAccountUrl($username) {
		return 'http://ffffound.com/home/'.$username.'/found/';
	}
	
	public function getContent($feeditem) {
		$raw_content = $feeditem->get_content();
        $content = str_replace('_m.jpg', '_s.jpg', $raw_content);
        return $content;
	}
	
	public function getFeedUrl($username) {
		#http://ffffound.com/home/lancew/found/feed

		return 'http://ffffound.com/home/'.$username.'/found/feed';	
	}
}