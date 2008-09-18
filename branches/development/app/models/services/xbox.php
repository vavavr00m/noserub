<?php
class XBoxService extends AbstractService {
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#live.xbox.com/member/(.+)#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://live.xbox.com/member/' . $username;
	}
}