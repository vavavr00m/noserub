<?php
class JabberService extends AbstractService {
	
	function detectService($url) {
		// GTalk Jabber addresses are not handled by this service
		if (strpos($url, '@gmail.com') !== false) {
			return false;
		}

		return $this->extractUsername($url, array('#xmpp:(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'xmpp:'.$username;
	}
}
?>