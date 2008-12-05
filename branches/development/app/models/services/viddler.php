<?php
class ViddlerService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#viddler.com/explore/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.viddler.com/explore/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.viddler.com/explore/'.$username.'/videos/feed/';
	}
}