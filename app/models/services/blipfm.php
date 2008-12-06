<?php
class BlipfmService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#blip.fm/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://blip.fm/'.$username.'/';
	}
	
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://blip.fm/feed/'.$username.'';
	}
}