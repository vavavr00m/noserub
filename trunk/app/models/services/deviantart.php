<?php
class DeviantartService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).deviantart.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.deviantart.com';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://backend.deviantart.com/rss.xml?q=gallery%3A'.$username.'+sort%3Atime&type=deviation';
	}
}