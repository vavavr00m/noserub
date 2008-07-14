<?php
class MagnoliaService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#ma.gnolia.com/people/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://ma.gnolia.com/people/'.$username.'/';
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromSinglePage('http://ma.gnolia.com/people/' . $username . '/contacts/', '/<a href="http:\/\/ma.gnolia.com\/people\/(.*)" class="fn url" rel="contact" title="Visit .*">.*<\/a>/iU');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://ma.gnolia.com/rss/full/people/'.$username.'/';
	}
}
?>