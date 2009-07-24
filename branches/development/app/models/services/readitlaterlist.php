<?php
class ReaditlaterlistService extends AbstractService {
	
	public function init() {
	    $this->name = 'Read It Later';
        $this->url = 'http://readitlaterlist.com/';
        $this->service_type = 2;
        $this->icon = 'readitlaterlist.gif';
        $this->has_feed = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#readitlaterlist.com/users/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://readitlaterlist.com/users/' . $username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://readitlaterlist.com/users/' . $username . '/feed/all';
	}
}