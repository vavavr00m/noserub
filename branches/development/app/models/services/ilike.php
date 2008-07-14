<?php
class IlikeService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#ilike.com/user/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://ilike.com/user/'.$username;
	}
	
	public function getContacts($username) {
		return ContactExtractor::getContactsFromMultiplePages('http://ilike.com/user/' . $username . '/friends', '/<a style=".*" class="person "  href="\/user\/(.*)" title="View .*\'s profile">/simU', '/src="\/images\/forward_arrow.gif" title="Go forward">/iU', '?page=');
	}
	
	public function getContent($feeditem) {
		return $feeditem->get_link();
	}
	
	public function getFeedUrl($username) {
		return 'http://ilike.com/user/'.$username.'/recently_played.rss';
	}
}
?>