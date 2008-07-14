<?php
class PlazesService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#plazes.com/users/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://plazes.com/users/'.$username;
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://plazes.com/users/' . $username . ';contacts', '/<em class="fn nickname">.*<a href="\/users\/.*" rel="vcard">\n(.*)\s{6}<\/a>/simU', '/next<\/a><\/strong><\/p>/iU', '?page=');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://plazes.com/users/'.$username.'/presences.atom';
	}
}
?>