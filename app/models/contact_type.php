<?php

uses('Sanitize');

class ContactType extends AppModel {
	var $hasAndBelongsToMany = array('Contact');
	
	function createContactTypes($identityId, $contactTypes) {
		$data['ContactType']['identity_id'] = $identityId;
		
		foreach ($contactTypes as $contactType) {
			$data['ContactType']['name'] = $contactType;
			$this->create($data);
			$this->save();
		}
	}
	
	function getContactTypesFromString($string) {
		if (empty($string)) {
			return array();
		}
		
		return array_unique(explode(' ', $string));
	}
	
	function getIDsOfContactTypes($identityId, $contactTypes) {
		$this->expects('ContactType');
		$contactTypeIDs = $this->findAll(array('ContactType.identity_id' => $identityId, 'ContactType.name' => $contactTypes), 'ContactType.id');
		$contactTypeIDs = Set::extract($contactTypeIDs, '{n}.ContactType.id');
		
		return $contactTypeIDs;
	}
	
	/**
	 * Returns those contact types which are not already in the database.
	 */
	function getNewContactTypes($identityId, $contactTypes) {
		$newContactTypes = array();
		
		$this->expects('ContactType');
		$existingContactTypes = $this->findAll(array('ContactType.identity_id' => $identityId, 'ContactType.name' => $contactTypes));
		$existingContactTypes = Set::extract($existingContactTypes, '{n}.ContactType.name');

		foreach ($contactTypes as $contactType) {
			if (!in_array($contactType, $existingContactTypes)) {
				$newContactTypes[] = $contactType;
			}
		}
		
		return $newContactTypes;
	}
	
	// TODO remove this function
	function getIds($identityId, $contactTypes) {
		$sanitizedContactTypes = $this->sanitizeContactTypes($contactTypes);

		$this->expects('ContactType');
		$ids = $this->findAll(array('ContactType.identity_id' => $identityId, 'ContactType.name' => $sanitizedContactTypes), 'ContactType.id');
		$ids = Set::extract($ids, '{n}.ContactType.id');
		
		return $ids;
	}
	
	// TODO remove this function
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
	
	// TODO remove this function
	private function sanitizeContactTypes($contactTypes) {
		$sanitized = array();
		
		foreach ($contactTypes as $contactType) {
			$sanitized[] = low(Sanitize::paranoid($contactType));
		}
		
		return $sanitized;
	}
}
?>