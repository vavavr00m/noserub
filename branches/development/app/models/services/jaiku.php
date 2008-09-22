<?php
class JaikuService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#(.+).jaiku.com#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://'.$username.'.jaiku.com';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://'.$username.'.jaiku.com', '/<a href="http:\/\/(.*).jaiku\.com\" class="url" rel="contact"/i');
	}
	
	public function getContent($feeditem) {
		# cut off the username
		$content = $feeditem->get_content();
        return substr($content, strpos($content, ': ') + 2);
	}
	
    public function getFeedUrl($username) {
	    return 'http://'.$username.'.jaiku.com/feed/rss';
	}
}