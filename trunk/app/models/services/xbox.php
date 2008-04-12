<?php
class XBoxService extends AbstractService {
	
	function detectService($url) {
		return $this->extractUsername($url, array('#live.xbox.com/member/(.+)#'));
	}
	
	function getAccountUrl($username) {
		return 'http://live.xbox.com/member/' . $username;
	}
}
?>