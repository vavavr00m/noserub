<?php
class ImthereService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#imthere.com/users/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://imthere.com/users/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://imthere.com/users/'.$username.'/events?format=rss';
	}
}