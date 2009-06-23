<?php
class SixgroupsService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#sixgroups.com/profile/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://sixgroups.com/profile/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://sixgroups.com/profile/'.$username.'/feed/rss/';
	}
}