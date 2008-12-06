<?php
class FavesService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#faves.com/users/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://faves.com/users/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://faves.com/users/'.$username.'/rss';
	}
}