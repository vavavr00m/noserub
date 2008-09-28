<?php
class GluggService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#http://glugg.no/bruker/view/profile/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://glugg.no/bruker/view/profile/'.$username.'/';
	}
	
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://glugg.no/rss/bruker/'.$username.'/all';


	}
}