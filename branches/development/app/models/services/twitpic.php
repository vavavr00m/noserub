<?php
class TwitpicService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#twitpic.com/photos/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://twitpic.com/photos/'.$username.'/';
	}
	
	public function getTitle($feeditem) {
	    # cut off the username
		$title = $feeditem->get_title();
        $title = substr($title, strpos($title, ': ') + 2);
        return $title;
	}
	
	public function getContent($feeditem) {
		# cut off the username
		$content = $feeditem->get_content();
        $content = substr($content, strpos($content, ': ') + 2);
        return $content;
	}
	
	public function getFeedUrl($username) {
	    return 'http://twitpic.com/photos/' . $username . '/feed.rss';
	}
}