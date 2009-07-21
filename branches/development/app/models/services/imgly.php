<?php
class ImglyService extends AbstractService {
	
	public function init() {
	    $this->name = 'img.ly';
        $this->url = 'http://img.ly/';
        $this->service_type = 1;
        $this->icon = 'imgly.gif';
        $this->has_feed = true;
	}
	
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