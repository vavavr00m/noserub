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

	function createAssociationsToContactTypes($contactId, $contactTypeIDs) {
		$dataToInsert['ContactTypesContact']['contact_id'] = $contactId;
		
		foreach ($contactTypeIDs as $contactTypeId) {
			$dataToInsert['ContactTypesContact']['contact_type_id'] = $contactTypeId;
			$this->ContactTypesContact->create($dataToInsert);
			$this->ContactTypesContact->save();
		}
	}

	function createAssociationsToNoserubContactTypes($contactId, $noserubContactTypeIDs) {
		$dataToInsert['ContactsNoserubContactType']['contact_id'] = $contactId;
		
		foreach ($noserubContactTypeIDs as $contactTypeId) {
			$dataToInsert['ContactsNoserubContactType']['noserub_contact_type_id'] = $contactTypeId;
			$this->ContactsNoserubContactType->create($dataToInsert);
			$this->ContactsNoserubContactType->save();
		}
	}
    
	function deleteAssociationsToContactTypes($contactId, $contactTypeIDs) {
		$this->ContactTypesContact->deleteAll(array('ContactTypesContact.contact_id' => $contactId, 'ContactTypesContact.contact_type_id' => $contactTypeIDs));
	}
	
	function deleteAssociationsToNoserubContactTypes($contactId, $noserubContactTypeIDs) {
		$this->ContactsNoserubContactType->deleteAll(array('ContactsNoserubContactType.contact_id' => $contactId, 'ContactsNoserubContactType.noserub_contact_type_id' => $noserubContactTypeIDs));
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
    
    public function export($identity_id) {
        $this->recursive = 1;
        $this->expects('Contact', 'WithIdentity', 'NoserubContactType', 'ContactType');
        $data = $this->findAllByIdentityId($identity_id);
        $contacts = array();
        foreach($data as $item) {
            $is_local = $item['WithIdentity']['is_local'];
            $is_private = $item['WithIdentity']['namespace'] ? 1 : 0;
            
            $contact = array(
                'username'   => $is_local ? $item['WithIdentity']['single_username'] : $item['WithIdentity']['username'],
                'is_local'   => $is_local,
                'is_private' => $is_private,
                'firstname'  => $item['WithIdentity']['firstname'],
                'lastname'   => $item['WithIdentity']['lastname'],
                'photo'      => $item['WithIdentity']['photo']
            );
            $list = array();
            foreach($item['NoserubContactType'] as $noserub_contact_type) {
                $list[] = $noserub_contact_type['name'];
            }
            $contact['noserub_contact_types'] = join(' ', $list);
            $list = array();
            foreach($item['ContactType'] as $contact_type) {
                $list[] = $contact_type['name'];
            }
            $contact['contact_types'] = join(' ', $list);
            
            if($is_local) {
                $contact['accounts'] = $this->Identity->Account->export($item['WithIdentity']['id']);
            }
            $contacts[] = $contact;
        }
        return $contacts;
    }
}
?>