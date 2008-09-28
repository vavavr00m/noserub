<?php

class OmbService extends AppModel {
	
	public function add($endPoints) {
		$postNoticeUrl = $endPoints[1][OMB_POST_NOTICE];
		$updateProfileUrl = $endPoints[1][OMB_UPDATE_PROFILE];
		
		$id = $this->field('id', array('OmbService.post_notice_url' => $postNoticeUrl, 'OmbService.update_profile_url' => $updateProfileUrl));
		
		if (!$id) {
			$data['OmbService']['post_notice_url'] = $postNoticeUrl;
			$data['OmbService']['update_profile_url'] = $updateProfileUrl;
			$this->create();
			$this->save($data);
			$id = $this->getLastInsertID();
		}
		
		return $id;
	}
}