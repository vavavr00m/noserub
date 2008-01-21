<?php
class OrkutService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#orkut.com/Profile.aspx\?uid=(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.orkut.com/Profile.aspx?uid='.$username;
	}
}
?>