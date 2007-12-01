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
}
?>