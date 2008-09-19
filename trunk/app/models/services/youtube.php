<?php
class YoutubeService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#youtube.com/user/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.youtube.com/user/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
	    return 'http://www.youtube.com/rss/user/' . $username . '/videos.rss';
	}
}