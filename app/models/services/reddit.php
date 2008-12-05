<?php
class RedditService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#reddit.com/user/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://reddit.com/user/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://reddit.com/user/'.$username.'/.rss';
	}
}