<?php
class YoutubeService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#youtube.com/user/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.youtube.com/user/'.$username.'/';
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
	    return 'http://www.youtube.com/rss/user/' . $username . '/videos.rss';
	}
}
?>