<?php
class ImglyService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#img.ly/images/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://img.ly/images/'.$username.'/';
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
	    return 'http://img.ly/images/' . $username . '.rss';
	}
}