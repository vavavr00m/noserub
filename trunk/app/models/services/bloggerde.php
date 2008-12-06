<?php
class BloggerdeService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).blogger.de#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.blogger.de/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://'.$username.'.blogger.de/rss?show=all';
	}
}