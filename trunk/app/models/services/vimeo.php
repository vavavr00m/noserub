<?php
class VimeoService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#vimeo.com/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://vimeo.com/'.$username.'/';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://vimeo.com/' . $username . '/contacts/', '/<div id="contact_(.*)">/iU', '/<img src="\/assets\/images\/paginator_right.gif" alt="next" \/><\/a>/iU', 'sort:date/page:');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	public function getFeedUrl($username) {
		return 'http://vimeo.com/'.$username.'/videos/rss/';
	}
}