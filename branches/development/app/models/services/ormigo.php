<?php
class OrmigoService extends AbstractService {
	
	public function init() {
	    $this->name = 'Ormigo';
        $this->url = 'http://ormigo.com/';
        $this->service_type_id = 5;
        $this->icon = 'ormigo.gif';
        $this->has_feed = false;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#ormigo.com/vcard/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.ormigo.com/vcard/'.$username.'/';
	}	
}