<?php
class SlideshareService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#slideshare.net/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.slideshare.net/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.slideshare.net/rss/user/'.$username;
	}
}