<?php

class OmbService extends AppModel {
	public $hasMany = array('OmbServiceAccessToken');
	public $validate = array('post_notice_url' => array('url'),
							 'update_profile_url' => array('url'));
	
	public function add($postNoticeUrl, $updateProfileUrl) {
		$data['OmbService']['post_notice_url'] = $postNoticeUrl;
		$data['OmbService']['update_profile_url'] = $updateProfileUrl;

		$this->create();
		
		if ($this->save($data)) {
			return $this->getLastInsertID();
		}
		
		return false;
	}
	
	// one param would be enough, but as I can't decide which param to use we will use both params ;-)
	public function getServiceId($postNoticeUrl, $updateProfileUrl) {
		return $this->field('id', array('OmbService.post_notice_url' => $postNoticeUrl, 
										'OmbService.update_profile_url' => $updateProfileUrl));
	}
}