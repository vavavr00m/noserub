<?php
class GithubService extends AbstractService {
	
	public function init() {
	    $this->name = 'Github';
        $this->url = 'http://github.com/';
        $this->service_type = 5;
        $this->icon = 'github.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#github.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://github.com/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		$content = $feeditem->get_content();
        return $content;
	}
	
	public function getFeedUrl($username) {
	    return 'http://github.com/' . $username . '.atom';
	}
}