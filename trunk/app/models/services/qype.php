<?php
class QypeService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#qype.com/people/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.qype.com/people/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.qype.com/people/'.$username.'/rss';
	}
}