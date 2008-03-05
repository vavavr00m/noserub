<?php
class IlikeService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#ilike.com/user/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://ilike.com/user/'.$username;
	}
	
	function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://ilike.com/user/' . $username . '/friends', '/<a style=".*" class="person "  href="\/user\/(.*)" title="View .*\'s profile">/simU', '/src="\/images\/forward_arrow.gif" title="Go forward">/iU', '?page=');
	}
	
	function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	function getFeedUrl($username) {
		return 'http://ilike.com/user/'.$username.'/recently_played.rss';
	}
}
?>