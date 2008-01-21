<?php
class XingService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#xing.com/profile/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'https://www.xing.com/profile/'.$username;
	}
}
?>