<?php
class FotologService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#www.fotolog.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.fotolog.com/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.fotolog.com/'.$username.'/feed/main/rss20';	
	}
}