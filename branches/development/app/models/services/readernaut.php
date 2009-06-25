<?php
class ReadernautService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#readernaut.com/(.+)/profile/#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://readernaut.com/'.$username.'/profile/';
	}
	
	public function getContent($feeditem) {
		# cut off the username
		$content = $feeditem->get_content();
        return substr($content, strpos($content, ': ') + 2);
	}
	
public function getFeedUrl($username) {
	    return 'http://readernaut.com/feeds/rss/'.$username.'/timeline/';

	}
}