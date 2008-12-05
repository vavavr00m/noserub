<?php
class MisterwongService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#mister-wong.de/user/(.+)/\?profile#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.mister-wong.de/user/'.$username.'/?profile';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.mister-wong.de/rss/user/'.$username.'/';
	}
}