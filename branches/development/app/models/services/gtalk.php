<?php
class GtalkService extends AbstractService {
	
	public function init() {
	    $this->name = 'GTalk';
        $this->url = 'http://www.google.com/talk/';
        $this->service_type_id = 3;
        $this->icon = 'gtalk.gif';
        $this->is_contact = true;
	}
	
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