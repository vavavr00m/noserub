<?php
class VimeoService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#vimeo.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://vimeo.com/'.$username.'/';
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://vimeo.com/'.$username.'/videos/rss/';
	}
}