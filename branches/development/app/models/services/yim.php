<?php
class YimService extends AbstractService {
	
	public function init() {
	    $this->name = 'YIM';
        $this->url = 'http://messenger.yahoo.com/';
        $this->icon = 'yim.gif';
        $this->is_contact = true;
	}
	
	public function detectService($url) {
		return $this->extractUsername($url, array('#edit.yahoo.com/config/send_webmesg\?.target=(.+)&.src=pg#'));
	}
	
	public function getAccountUrl($username) {
		return 'http://edit.yahoo.com/config/send_webmesg?.target='.$username.'&.src=pg';
	}
}