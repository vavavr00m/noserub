<?php

class OmbLocalService extends AppModel {
	public $hasMany = array('OmbLocalServiceAccessToken');
	
	public function add(OmbLocalServiceDefinition $serviceDefinition) {
		$data['OmbLocalService']['post_notice_url'] = $serviceDefinition->getPostNoticeUrl();
		$data['OmbLocalService']['update_profile_url'] = $serviceDefinition->getUpdateProfileUrl();

		$this->create();
		
		if ($this->save($data)) {
			return $this->getLastInsertID();
		}
		
		return false;
	}
	
	public function getServiceId(OmbLocalServiceDefinition $serviceDefinition) {
		return $this->field('id', array('OmbLocalService.post_notice_url' => $serviceDefinition->getPostNoticeUrl(), 
										'OmbLocalService.update_profile_url' => $serviceDefinition->getUpdateProfileUrl()));
	}
}