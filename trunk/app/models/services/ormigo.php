<?php
class OrmigoService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#ormigo.com/vcard/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.ormigo.com/vcard/'.$username.'/';
	}	
}
?>