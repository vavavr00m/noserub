<?php
class ZooomrService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#zooomr.com/photos/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.zooomr.com/photos/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.zooomr.com/people/' . $username . '/contacts/', '/View their <a href="\/people\/(.*)\/">profile<\/a><\/p>/iU');
	}
	
	function getContent($feeditem) {
		$raw_content = $feeditem->get_content();
        if(preg_match('/<img src="(.*)_m\.jpg"/iUs', $raw_content, $matches)) {
            return '<a href="'.$feeditem->get_link().'"><img src="'.$matches[1].'_s.jpg" /></a>';
        }
        return '';
	}
	
	function getFeedUrl($username) {
		return 'http://www.zooomr.com/services/feeds/public_photos/?id='.$username.'&format=rss_200';
	}
}
?>