<?php
class GadugaduService extends AbstractService {
	
	public function init() {
	    $this->name = 'Gadu-Gadu';
        $this->url = 'http://www.gadu-gadu.pl/';
        $this->service_type = 3;
        $this->icon = 'gadugadu.gif';
        $this->is_contact = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('/^gg:(.+)/'));
	}
	
	public function getAccountUrl($username) {
		return 'gg:'.$username;
	}
}