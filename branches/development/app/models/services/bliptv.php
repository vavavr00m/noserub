<?php
class BliptvService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).blip.tv#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.blip.tv/';
	}
	
#	public function getContacts($username) {
#		return ContactExtractor::getContactsFromSinglePage('http://www.viddler.com/explore/' . $username . '/friends/', '/<p><strong><a.*href="\/explore\/.*\/".*>(.*)<\/a>/iU');
#	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://'.$username.'.blip.tv/rss/';
	}
}