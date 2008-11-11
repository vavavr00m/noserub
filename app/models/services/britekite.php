<?php
class BritekiteService extends AbstractService {
	

	public function detectService($url) {
		return $this->extractUsername($url, array('#brightkite.com/people/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://brightkite.com/people/'.$username;
	}
	

	
	public function getContent($feeditem) {

		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://brightkite.com/people/'.$username.'/objects.rss';
	}
	
}