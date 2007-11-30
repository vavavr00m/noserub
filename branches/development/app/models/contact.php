<?php
/* SVN FILE: $Id:$ */
 
class Contact extends AppModel {
    var $belongsTo = array('Identity',
                           'WithIdentity' => array('className' => 'Identity',
                                                   'foreignKey' => 'with_identity_id'));

    var $hasAndBelongsToMany = array('ContactType', 'NoserubContactType');
    
    var $validate = array(
            'username' => array('content'  => array('rule' => array('custom', NOSERUB_VALID_USERNAME)),
                                'required' => VALID_NOT_EMPTY)
        );

    /**
     * @param array $data Array of ContactTypeIds
     */
	function createAssociationsToContactTypes($contactId, $data) {
		$dataToInsert['ContactTypesContact']['contact_id'] = $contactId;
		
		foreach ($data as $contactTypeId) {
			$dataToInsert['ContactTypesContact']['contact_type_id'] = $contactTypeId;
			$this->ContactTypesContact->create($dataToInsert);
			$this->ContactTypesContact->save();
		}
	}
        
	/**
	 * @param array $data Array of NoserubContactTypeIds
	*/
	function createAssociationsToNoserubContactTypes($contactId, $data) {
		$dataToInsert['ContactsNoserubContactType']['contact_id'] = $contactId;
		
		foreach ($data as $contactTypeId) {
			$dataToInsert['ContactsNoserubContactType']['noserub_contact_type_id'] = $contactTypeId;
			$this->ContactsNoserubContactType->create($dataToInsert);
			$this->ContactsNoserubContactType->save();
		}
	}
    
	/**
	 * @param array $data Array of NoserubContactTypeIds
	 */
	function deleteAssociationsToNoserubContactTypes($contactId, $data) {
		$this->ContactsNoserubContactType->deleteAll(array('ContactsNoserubContactType.noserub_contact_type_id' => $data, 'ContactsNoserubContactType.contact_id' => $contactId));
	}
	
    /**
     * Deletes all contacts from and to this identity_id
     * Also deletes all private contact's identites, accounts and feeds
     * @todo it is not very good practice to delete data from models other
     *       than Contact here. 
     * @param  int $identity_id
     * @return 
     * @access 
     */
    function deleteByIdentityId($identity_id, $local_username) {
        $this->recursive = 1;
        $this->expects('Contact.Contact', 'Contact.WithIdentity', 'WithIdentity.WithIdentity');
        $contacts = $this->findAll(array('identity_id=' . $identity_id . ' OR with_identity_id=' . $identity_id));
        foreach($contacts as $contact) {
            if($contact['Contact']['identity_id'] == $identity_id &&
               $contact['WithIdentity']['namespace'] == $local_username) {
                # this is a private contact of this identity
                # we need to delete the private identity, thier accounts and feeds
                $this->Identity->Account->deleteByIdentityId($contact['WithIdentity']['id']);
                $this->Identity->delete($contact['WithIdentity']['id']);
            }
            
            # the contact itself can be removed in all cases
            $this->delete($contact['Contact']['id']);
        }
    }
    
    function getIdsOfSelectedNoserubContactTypes($contactId) {
    	$contactTypes = $this->ContactsNoserubContactType->findAllByContactId($contactId);
    	$ids = Set::extract($contactTypes, '{n}.ContactsNoserubContactType.noserub_contact_type_id');
    	
    	return $ids;
    }
    
    function updateSelectedNoserubContactTypes($contactId, $data) {
    	$currentlySelected = $this->getIdsOfSelectedNoserubContactTypes($contactId);
    	$toCreate = array();
    	$toRemove = array();
    	
    	foreach ($data as $contactTypeId => $selected) {
    		if ($selected && !in_array($contactTypeId, $currentlySelected)) {
    			$toCreate[] = $contactTypeId;
    		} elseif (!$selected && in_array($contactTypeId, $currentlySelected)) {
    			$toRemove[] = $contactTypeId;
    		}
    	}
    	
    	if (!empty($toCreate)) {
    		$this->createAssociationsToNoserubContactTypes($contactId, $toCreate);
    	}
    	
    	if (!empty($toRemove)) {
    		$this->deleteAssociationsToNoserubContactTypes($contactId, $toRemove);
    	}
    }
}
?>