<?php

class OmbService extends AppModel {
	public $hasMany = array('OmbServiceAccessToken');
	
	public function add(OmbLocalServiceDefinition $serviceDefinition) {
		$data['OmbService']['post_notice_url'] = $serviceDefinition->getPostNoticeUrl();
		$data['OmbService']['update_profile_url'] = $serviceDefinition->getUpdateProfileUrl();

		$this->create();
		
		if ($this->save($data)) {
			return $this->getLastInsertID();
		}
		
		return false;
	}
	
	public function getServiceId(OmbLocalServiceDefinition $serviceDefinition) {
		return $this->field('id', array('OmbService.post_notice_url' => $serviceDefinition->getPostNoticeUrl(), 
										'OmbService.update_profile_url' => $serviceDefinition->getUpdateProfileUrl()));
	}
}