<?php
/* SVN FILE: $Id:$ */
 
class Contact extends AppModel {
    public $belongsTo = array('Identity',
                           'WithIdentity' => array('className' => 'Identity',
                                                   'foreignKey' => 'with_identity_id'));

    public $hasAndBelongsToMany = array('ContactType', 'NoserubContactType');
    
    public $validate = array(
            'username' => array('content'  => array('rule' => array('custom', NOSERUB_VALID_USERNAME)),
                                'required' => VALID_NOT_EMPTY)
        );

    /**
     * creates a contact, when it's not already there
     */
    public function add($identity_id, $with_identity_id) {
        $this->contain();
        $conditions = array(
            'identity_id'      => $identity_id,
            'with_identity_id' => $with_identity_id
        );
        
        $contact = $this->find($conditions, array('id'));
        if(!$contact) {
            $this->create();
            $data = $conditions;
            
            return $this->save($data, true, array_keys($data));
        }
        
        # don't return with an error, if we already have the contact
        $this->id = $contact['Contact']['id'];
        return true;
    }
    
	public function createAssociationsToContactTypes($contact_id, $contact_type_ids) {
		$data['contact_id'] = $contact_id;
		
		foreach($contact_type_ids as $contact_type_id) {
		    $data['contact_type_id'] = $contact_type_id;
		    
		    # check, if we already have that
		    $this->ContactTypesContact->cacheQueries = false;
		    $conditions = $data;
		    if(!$this->ContactTypesContact->hasAny($conditions)) {
			    $this->ContactTypesContact->create();
			    $this->ContactTypesContact->save($data);
		    }
		}
	}

	public function createAssociationsToNoserubContactTypes($contact_id, $noserub_contact_type_ids) {
		$data['contact_id'] = $contact_id;
		
		foreach ($noserub_contact_type_ids as $noserub_contact_type_id) {
			$data['noserub_contact_type_id'] = $noserub_contact_type_id;
			
			# check, if we already have that
		    $this->ContactsNoserubContactType->cacheQueries = false;
		    $conditions = $data;
		    if(!$this->ContactsNoserubContactType->hasAny($conditions)) {
			    $this->ContactsNoserubContactType->create();
			    $this->ContactsNoserubContactType->save($data);
		    }
		}
	}
    
	public function deleteAssociationsToContactTypes($contactId, $contactTypeIDs) {
		$this->ContactTypesContact->deleteAll(array('ContactTypesContact.contact_id' => $contactId, 'ContactTypesContact.contact_type_id' => $contactTypeIDs));
	}
	
	public function deleteAssociationsToNoserubContactTypes($contactId, $noserubContactTypeIDs) {
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
    public function deleteByIdentityId($identity_id, $local_username) {
        $this->contain('WithIdentity');
        $contacts = $this->find('all', array('conditions' => array('identity_id=' . $identity_id . ' OR with_identity_id=' . $identity_id)));

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
        $this->contain(array('WithIdentity', 'NoserubContactType', 'ContactType'));
        $data = $this->findAllByIdentityId($identity_id);
        $contacts = array();
        foreach($data as $item) {
            $is_local   = $item['WithIdentity']['is_local'];
            $is_private = $item['WithIdentity']['namespace'] ? 1 : 0;
            
            $contact = array(
                'username'   => $is_private ? $item['WithIdentity']['single_username'] : $item['WithIdentity']['username'],
                'is_local'   => $is_local,
                'is_private' => $is_private,
                'firstname'  => $item['WithIdentity']['firstname'],
                'lastname'   => $item['WithIdentity']['lastname'],
                'photo'      => $item['WithIdentity']['photo'],
                'note'       => $item['Contact']['note']
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
            
            if($is_private) {
                $contact['accounts'] = $this->Identity->Account->export($item['WithIdentity']['id']);
            }
            $contacts[] = $contact;
        }
        return $contacts;
    }
    
    /**
     * Import the contacts and also create accounts for those
     * contacts.
     */
    public function import($identity_id, $data) {
        # get identity first
        $this->Identity->contain();
        $this->Identity->id = $identity_id;
        $identity = $this->Identity->read();

        foreach($data as $item) {
            # create username
            $username = $item['username'];
            if($item['is_private']) {
                $username = $username . '@' . $identity['Identity']['local_username'];
            }
            $new_splitted = $this->Identity->splitUsername($username, $item['is_local']);
        
            # check, if we already have that username
            $this->Identity->contain();
            $new_identity = $this->Identity->findByUsername($new_splitted['username']);
            
            if(!$new_identity) {
                # we need to create the contact
                $this->Identity->create();
                $new_identity = array('username' => $new_splitted['username']);
                $this->Identity->save($new_identity, false, array_keys($new_identity));
                $new_identity_id = $this->Identity->id;
                
                # get user data
                if(!$item['is_private']) {
                    $result = $this->requestAction('/jobs/' . NOSERUB_ADMIN_HASH . '/sync/identity/' . $new_identity_id . '/');
                }
            } else {
                $new_identity_id = $new_identity['Identity']['id'];
            }
            
            # make sure we add the contact
            if(!$this->add($identity_id, $new_identity_id)) {
                return false;
            }

            # just make sure, that we keep the note
            if(isset($item['note']) && $item['note']) {
                $this->contain();
                $this->cacheQueries = false;
                $contact = $this->read();
                if(!$contact['Contact']['note']) {
                    $this->saveField('note', $item['note']);
                }
            }
            
            # add contact types
            if(isset($item['contact_types']) && $item['contact_types']) {
                $contact_type_ids = $this->ContactType->createFromString($identity_id, $item['contact_types']);
                
                if($contact_type_ids) {
                    $this->createAssociationsToNoserubContactTypes($this->id, $contact_type_ids);
                }
            }
            
            # add noserub contact types
            # but only, if there are none set here right now
            if(isset($item['noserub_contact_types']) && $item['noserub_contact_types']) {
                $this->contain(array('Contact', 'NoserubContactType'));
                $this->cacheQueries = false;
                $contact = $this->read();
                if(count($contact['NoserubContactType']) == 0) {
                    $result = $this->NoserubContactType->extract($item['noserub_contact_types']);
                    if($result['noserub_contact_type_ids']) {
                        # re-arrange the array
                        $to_add = array();
                        foreach($result['noserub_contact_type_ids'] as $id => $dummy) {
                            $to_add[] = $id;
                        }
                        $this->createAssociationsToNoserubContactTypes($this->id, $to_add);
                    }
                }
            }
            
            # add accounts
            if($item['is_private'] && 
               isset($item['accounts']) &&
               $item['accounts']) {
                $this->log(print_r($item['accounts'], 1));
                $this->Identity->Account->update($new_identity_id, $item['accounts']);
            }
        }
        
        return true;
    }
}
?>