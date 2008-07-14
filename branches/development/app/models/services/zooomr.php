<?php
class ZooomrService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#zooomr.com/photos/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.zooomr.com/photos/'.$username;
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://www.zooomr.com/people/' . $username . '/contacts/', '/View their <a href="\/people\/(.*)\/">profile<\/a><\/p>/iU');
	}
	
	public function getContent($feeditem) {
		$raw_content = $feeditem->get_content();
        if(preg_match('/<img src="(.*)_m\.jpg"/iUs', $raw_content, $matches)) {
            return '<a href="'.$feeditem->get_link().'"><img src="'.$matches[1].'_s.jpg" /></a>';
        }
        return '';
	}
	
	public function getFeedUrl($username) {
		return 'http://www.zooomr.com/services/feeds/public_photos/?id='.$username.'&format=rss_200';
	}
}
?>