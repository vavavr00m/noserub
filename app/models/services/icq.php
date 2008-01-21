<?php
class IcqService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#icq.com/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://www.icq.com/'.$username;
	}
}
?>