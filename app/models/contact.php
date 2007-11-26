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
        
	// TODO removing iterator and foreach
	function createAssociationsToNoserubContactTypes($contactId, $data) {
		$iterator = new NoserubContactTypesFilter($data);
		
		foreach ($iterator as $key => $value) {
			if (is_numeric($key)) {
				$this->query('INSERT INTO contacts_noserub_contact_types (contact_id, noserub_contact_type_id) values ('.$contactId.', '.$key.')');
			}
		}
	}
    
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
    			$toCreate[$contactTypeId] = 1;
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

class NoserubContactTypesFilter extends FilterIterator {
	private $filter = 1;
	
	public function __construct($array) {
		parent::__construct(new ArrayIterator($array));
	}
	
	public function accept() {
		return ($this->current() == $this->filter);
    }
}