<?php
class VimeoService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#vimeo.com/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://vimeo.com/'.$username.'/';
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://vimeo.com/' . $username . '/contacts/', '/<div id="contact_(.*)">/iU', '/<img src="\/assets\/images\/paginator_right.gif" alt="next" \/><\/a>/iU', 'sort:date/page:');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_content();
	}
	
	function getFeedUrl($username) {
		return 'http://vimeo.com/'.$username.'/videos/rss/';
	}
}
?>