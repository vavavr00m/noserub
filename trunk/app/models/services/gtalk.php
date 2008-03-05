<?php
class GtalkService extends AbstractService {
	
	function detectService($url) {
		if (strpos($url, '@gmail.com') === false) {
			return false;
		}
		
		return $this->extractUsername($url, array('#xmpp:(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'xmpp:'.$username;
	}
}
?>