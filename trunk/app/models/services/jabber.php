<?php
class JabberService extends AbstractService {
	
	public function init() {
	    $this->name = 'Jabber';
        $this->url = 'http://jabber.org/';
        $this->icon = 'jabber.gif';
        $this->is_contact = true;
	}
	
	public function detectService($url) {
		// GTalk Jabber addresses are not handled by this service
		if (strpos($url, '@gmail.com') !== false) {
			return false;
		}

		return $this->extractUsername($url, array('#xmpp:(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'xmpp:'.$username;
	}
}