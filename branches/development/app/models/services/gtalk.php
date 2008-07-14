<?php
class GtalkService extends AbstractService {
	
	public function detectService($url) {
		if (strpos($url, '@gmail.com') === false) {
			return false;
		}
		
		return $this->extractUsername($url, array('#xmpp:(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'xmpp:'.$username;
	}
}
?>