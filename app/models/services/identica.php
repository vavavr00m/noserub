<?php
class IdenticaService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#identi.ca/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://identi.ca/'.$username;
	}
	
	#public function getContacts($username) {
	#	return ContactExtractor::getContactsFromSinglePage('http://twitter.com/' . $username . '/', '/<a href="http:\/\/twitter\.com\/(.*)" class="url" rel="contact"/i');
	#}
	
	public function getContent($feeditem) {
		# cut off the username
		$content = $feeditem->get_title();
        return substr($content, strpos($content, ': ') + 2);
	}
	
	public function getFeedUrl($username) {
	    return 'http://identi.ca/'.$username.'/rss';
	}
}
?>