<?php
class MentoService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#www.mento.info/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.mento.info/'.$username;
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.mento.info/feeds/public/'.$username;
	}
}