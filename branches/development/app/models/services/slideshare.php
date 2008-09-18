<?php
class SlideshareService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#slideshare.net/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.slideshare.net/'.$username;
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://www.slideshare.net/' . $username . '/contacts', '/<a href="\/(.*)" style="" title="" class="blue_link_normal" id="">.*<\/a>/iU', '/class="text_float_left">Next<\/a>/iU', '/');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://www.slideshare.net/rss/user/'.$username;
	}
}