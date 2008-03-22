<?php

uses('Sanitize');

class ContactType extends AppModel {
	var $hasAndBelongsToMany = array('Contact');
	
	/**
	 * return ids of contact types for the tags in
	 * the string. also removes thos tags from the new tags.
	 *
	 * @param array $new_tag 
	 * @return array of contact type ids and cleaned up tags
	 */
	public function extract($tags) {
	    $ids = array();
	    $remaining_tags = array();
	    foreach($tags['tags'] as $tag) {
	        if($tag) {
	            $this->recursive = 0;
	            $this->expects('ContactType');
	            $data = $this->findByName($tag, array('id'));
                if($data) {
                    $ids[$data['ContactType']['id']] = 1;
                } else {
                    $remaining_tags[] = $tag;
                }
            }
	    }

	    return array('contact_type_ids'         => $ids,
	                 'noserub_contact_type_ids' => $tags['noserub_contact_type_ids'],
	                 'tags'                     => $remaining_tags);
	}
	
	/**
     * merges the array of contact types (id => {0,1}) with
     * the tags in the string $new_tags
     */
	public function merge($contact_types, $new_tag_ids) {
	    foreach($contact_types as $id => $marked) {
	        if(isset($new_tag_ids[$id])) {
	            $contact_types[$id] = 1;
	        }
	    }
	    
	    return $contact_types;
	}
	
	/**
	 * removes a contact type, when it is no longer being used
	 */
	public function removeIfUnused($contact_type_id) {
	    $this->ContactTypesContact->recursive = 0;
	    $this->ContactTypesContact->expects('ContactTypesContact');
	    if(!$this->findCount(array('contact_type_id' => $contact_type_id))) {
	        $this->id = $contact_type_id;
	        $this->delete();
	    }
	}
	
	public function createContactTypes($identity_id, $contact_types) {
	    $contact_type_ids = array();
		$data['ContactType']['identity_id'] = $identity_id;
		
		foreach ($contact_types as $contact_type_name) {
		    # check, if we already have it
		    $this->recursive = 0;
		    $this->expects('ContactType');
		    $conditions = array(
		        'identity_id' => $identity_id,
		        'name'        => $contact_type_name
		    );
		    $this->cacheQueries = false;
		    $contact_type = $this->find($conditions, array('id'));
		    if(!$contact_type) {
		        $data = $conditions;
			    $this->create();
			    $this->save($data);
			    $contact_type_ids[] = $this->id;
		    } else {
		        $contact_type_ids[] = $contact_type['ContactType']['id'];
		    }
		}
		
		return $contact_type_ids;
	}
	
	public function createFromString($identity_id, $string) {
	    $contact_types = $this->getContactTypesFromString($string);
	    return $this->createContactTypes($identity_id, $contact_types);
	}
	
	function deleteContactTypes($contactTypeIDs) {
		$this->deleteAll(array('ContactType.id' => $contactTypeIDs));
	}
	
	function getContactTypesFromString($string) {
		if (empty($string)) {
			return array();
		}
		
		$contact_types = array_unique(explode(' ', $string)); 
		$sanitized_contact_types = array();
		
		foreach($contact_types as $contact_type) {
			$sanitized_contact_types[] = Sanitize::paranoid($contact_type);
		}
		
		return $sanitized_contact_types; 
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