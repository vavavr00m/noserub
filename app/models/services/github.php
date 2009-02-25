<?php
class GithubService extends AbstractService {
	
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