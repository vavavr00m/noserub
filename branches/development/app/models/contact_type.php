<?php

uses('Sanitize');

class ContactType extends AppModel {
	var $hasAndBelongsToMany = array('Contact');
	
	function getIds($identityId, $contactTypes) {
		$sanitizedContactTypes = $this->sanitizeContactTypes($contactTypes);

		$this->expects('ContactType');
		$ids = $this->findAll(array('ContactType.identity_id' => $identityId, 'ContactType.name' => $sanitizedContactTypes), 'ContactType.id');
		$ids = Set::extract($ids, '{n}.ContactType.id');
		
		return $ids;
	}
	
	function saveIfNotExisting($identityId, $contactTypes) {
		$sanitizedContactTypes = $this->sanitizeContactTypes($contactTypes);
		
		foreach ($sanitizedContactTypes as $contactType) {
			if (!$this->hasAny(array('ContactType.identity_id' => $identityId, 'ContactType.name' => $contactType))) {
				$data['ContactType']['identity_id'] = $identityId;
				$data['ContactType']['name'] = $contactType;
				$this->create($data);
				$this->save();
			}
		}
	}
	
	private function sanitizeContactTypes($contactTypes) {
		$sanitized = array();
		
		foreach ($contactTypes as $contactType) {
			$sanitized[] = low(Sanitize::paranoid($contactType));
		}
		
		return $sanitized;
	}
}
?>