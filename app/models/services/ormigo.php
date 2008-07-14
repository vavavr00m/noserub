<?php
class OrmigoService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#ormigo.com/vcard/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://www.ormigo.com/vcard/'.$username.'/';
	}	
}
?>