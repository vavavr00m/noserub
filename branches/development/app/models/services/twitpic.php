<?php
class TwitpicService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#twitpic.com/photos/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://twitpic.com/photos/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
	    return 'http://twitpic.com/photos/' . $username . '/feed.rss';
	}
}