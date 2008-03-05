<?php
class PlazesService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#plazes.com/users/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://plazes.com/users/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://plazes.com/users/' . $username . ';contacts', '/<em class="fn nickname">.*<a href="\/users\/.*" rel="vcard">\n(.*)\s{6}<\/a>/simU', '/next<\/a><\/strong><\/p>/iU', '?page=');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://plazes.com/users/'.$username.'/presences.atom';
	}
}
?>