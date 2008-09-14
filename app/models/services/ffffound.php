<?php
class FfffoundService extends AbstractService {
	
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
?>