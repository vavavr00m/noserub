<?php
class PosterousService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).posterous.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.posterous.com/';
	}
	
	public function getTitle($feeditem) {
		return $feeditem->get_title();
	}
	
	public function getFeedUrl($username) {
	    return 'http://'.$username.'.posterous.com/rss.xml';
	}
}