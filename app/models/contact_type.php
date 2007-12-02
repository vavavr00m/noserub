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
	
	function deleteContactTypes($contactTypeIDs) {
		$this->deleteAll(array('ContactType.id' => $contactTypeIDs));
	}
	
	function getContactTypesFromString($string) {
		if (empty($string)) {
			return array();
		}
		
		$contactTypes = array_unique(explode(' ', $string)); 
		$sanitizedContactTypes = array();
		
		foreach($contactTypes as $contactType) {
			$sanitizedContactTypes[] = Sanitize::paranoid($contactType);
		}
		
		return $sanitizedContactTypes; 
	}
	
	function getIDsOfContactTypes($identityId, $contactTypes) {
		$this->expects('ContactType');
		$contactTypeIDs = $this->findAll(array('ContactType.identity_id' => $identityId, 'ContactType.name' => $contactTypes), 'ContactType.id');
		$contactTypeIDs = Set::extract($contactTypeIDs, '{n}.ContactType.id');
		
		return $contactTypeIDs;
	}
	
	function getIDsOfUnusedContactTypes($contactTypeIDs) {
		$usedContactTypeIDs = $this->ContactTypesContact->findAll(array('ContactTypesContact.contact_type_id' => $contactTypeIDs));
		$usedContactTypeIDs = array_unique(Set::extract($usedContactTypeIDs, '{n}.ContactTypesContact.contact_type_id'));
		
		$unusedContactTypeIDS = array();
		
		foreach ($contactTypeIDs as $contactTypeID) {
			if (!in_array($contactTypeID, $usedContactTypeIDs)) {
				$unusedContactTypeIDS[] = $contactTypeID;
			}
		}
		
		return $unusedContactTypeIDS;
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