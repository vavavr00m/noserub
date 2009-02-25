<?php
class GooglecodeService extends AbstractService {
	public function detectService($url) {
	#http://code.google.com/u/dirk.olbertz/
		return $this->extractUsername($url, array('#code.google.com/u/(.+)/updates#','#code.google.com/u/(.+)#',));
	}
	
	public function getAccountUrl($username) {
		return 'http://code.google.com/u/'.$username;
	}
	
	public function getTitle($feeditem) {
		return strip_tags($feeditem->get_content());
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_title();
	}
	
	public function getFeedUrl($username) {
		return 'http://code.google.com/feeds/u/' . $username . '/updates/user/basic';
	}
}