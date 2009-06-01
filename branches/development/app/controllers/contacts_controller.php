<?php
/* SVN FILE: $Id:$ */
 
class ContactsController extends AppController {
    public $uses = array('Contact');
    public $helpers = array('nicetime', 'flashmessage', 'xfn');
    public $components = array('cluster');
    
    public function index() {
        $this->checkUnsecure();
        
        Context::setPage('profile.contacts');
    }
    
    /**
     * adds a new contact to an identity
     * todo: check for existing identities
     */
    public function add() {
        $this->grantAccess('user');
        
        if($this->data) {
            $this->Contact->data = $this->data;
            $identity_username = trim($this->data['Contact']['noserub_id']);
            $identity_username_splitted = $this->Contact->Identity->splitUsername($identity_username, false);

            # so, check, if this is really the case
            if($identity_username === '') {
                $this->Contact->invalidate('noserub_id', 'no_valid_noserub_id');
                $this->render();
                return;
            }
            
            # see, if we already have it
            $identity = $this->Contact->Identity->findByUsername($identity_username_splitted['username']);
            if(!$identity) {
                # no, so create the new identity
                $this->Contact->Identity->create();
                $identity = array('username' => $identity_username_splitted['username']);
                $saveable = array('username');
                $this->Contact->Identity->save($identity, false, $saveable);
                $new_identity_id = $this->Contact->Identity->id;
                
                # get user data
                $result = $this->requestAction('/jobs/' . Configure::read('NoseRub.admin_hash') . '/sync/identity/' . $new_identity_id . '/');
                if($result == false) {
                    # user could not be found, so delete it
                    $this->Contact->Identity->id = $new_identity_id;
                    $this->Contact->Identity->delete();
                    $this->flashMessage('error', __('Could not add contact.', true));
                    $this->Contact->invalidate('noserub_id', 'user_not_found');
                    $this->render();
                    return;
                }
            } else {
                # it's already there, so we can go ahead and add it
            	$new_identity_id = $identity['Identity']['id'];                	
            }
            
            # now create the contact relationship
            
            # but first make sure, that this connection is not already there
            if($this->Contact->hasAny(
               array(
                    'identity_id'      => Context::loggedInIdentityId(), 
                    'with_identity_id' => $new_identity_id
               ))) {
                $this->Contact->invalidate('noserub_id', 'unique');
                $this->render();
                return;
            }
            
            if($this->Contact->add(Context::loggedInIdentityId(), $new_identity_id)) {
                $this->Contact->Identity->Entry->addNewContact(Context::loggedInIdentityId(), $new_identity_id, null);
                $this->flashMessage('success', __('New contact added.', true));
			    $this->Session->write('Contacts.add.Contact.id', $this->Contact->id);
			    $this->redirect('/contacts/' . $this->Contact->id . '/edit/');
		    } else {
		        $this->flashMessage('error', __('Could not add contact.', true));
		    }
        }        
    }
        
    public function delete() {
        $this->grantAccess('user');
        $contact_id        = isset($this->params['contact_id']) ? $this->params['contact_id'] : '';
        
        # make sure, that the correct security token is set
        $this->ensureSecurityToken();
        
        # check, if the contact belongs to the identity
        $this->Contact->contain();
        $contact = $this->Contact->find(array('id'          => $contact_id,
                                              'identity_id' => Context::loggedInIdentityId()));
    
        if(!$contact) {
            # contact not found for logged in user
            $this->redirect('/contacts/');
        }
        
        $this->Contact->deleteContactTypeAssociations($contact_id);
        $this->Contact->OmbAccessToken->deleteByContactId($contact_id);
        $this->Contact->OmbLocalServiceAccessToken->deleteByContactId($contact_id);
        
        # remove this contact
        $with_identity_id = $contact['Contact']['with_identity_id'];
        $this->Contact->id = $contact_id;
        $this->Contact->delete();
        $this->flashMessage('success', __('Removed the contact.', true));

        $this->redirect('/contacts/');
    }
    
    /**
     * Display some information about the contact. Especially for the
     * external contacts, we want to show their accounts and maybe other
     * data we have about that identity.
     */
    public function info() {
        $this->grantAccess('user');
        
        $contact_id = isset($this->params['contact_id']) ? $this->params['contact_id'] : '';
    	$username   = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted   = $this->Contact->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
         
        # get the contact
		$this->Contact->contain('Identity', 'WithIdentity');
	    $contact = $this->Contact->findById($contact_id);
	    
        if($session_identity['id'] != $contact['Contact']['identity_id']) {
            # this is not a contact of the logged in user
            $this->redirect('//contacts/');
        }
        
        $this->set('contact', $contact);
        $this->set('contact_photo', $this->Contact->Identity->getPhotoUrl($contact, 'WithIdentity'));
        
        # get contact's accounts
        $this->Contact->Identity->Account->contain('Service');
        $this->set('accounts', $this->Contact->Identity->Account->findAllByIdentityId($contact['WithIdentity']['id']));
        
        $this->set('headline', sprintf('Info about %s', $contact['WithIdentity']['username']));
    }
    
    public function edit() {
        $this->grantAccess('user');
         
    	$contact_id = isset($this->params['contact_id']) ? $this->params['contact_id'] : '';
    	$username   = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted   = $this->Contact->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        # get the contact
	    $this->Contact->contain(array('Identity', 'WithIdentity', 'ContactType', 'NoserubContactType'));
	    $contact = $this->Contact->findById($contact_id);
	    
        if(Context::loggedInIdentityId() != $contact['Contact']['identity_id']) {
            # this is not a contact of the logged in user
            $this->redirect('/contacts/');
        }
    	
    	# get currently selected types (pre- and user-defined)
    	$selected_contact_types = Set::extract($contact['ContactType'], '{n}.id');
	    $this->set('selected_contact_types', Set::extract($contact['ContactType'], '{n}.id'));
	    $selected_noserub_contact_types = Set::extract($contact['NoserubContactType'], '{n}.id');
	    $this->set('selected_noserub_contact_types', $selected_noserub_contact_types);
	    
    	if($this->data) {
    	    if($this->data['Contact']['note'] != $contact['Contact']['note']) {
    	        $this->Contact->id = $contact_id;
    	        $this->Contact->saveField('note', $this->data['Contact']['note']);
    	    }
    	    
            # transform the xfn data to the noserub contact typ
            # (not so nice, but working for now)
            $xfn_ids = array(1, 2, 3, 4, 5, 8, 9, 10, 11, 12, 13, 14);
            for($i=0; $i<count($xfn_ids); $i++) {
                $this->data['NoserubContactType'][$xfn_ids[$i]] = 0;
            }
            foreach($this->data['xfn'] as $id) {
                if($id) {
                    $this->data['NoserubContactType'][$id] = 1;
                }
            }
            
            
    	    # extract noserub contact types from new tags and clean them up
    	    $new_tags = $this->Contact->NoserubContactType->extract($this->data['Tags']['own']);
    	    
    	    # extract contact types from new tags and clean them up
    	    $new_tags = $this->Contact->ContactType->extract(Context::loggedInIdentityId(), $new_tags);
    	    
    	    # merge manual tags with noserub contact types
    	    $entered_noserub_contact_types = $this->Contact->NoserubContactType->merge($this->data['NoserubContactType'], $new_tags['noserub_contact_type_ids']);
    	    
    	    # merge manual tags with contact types
    	    if(isset($this->data['ContactType'])) {
    	        $entered_contact_types = $this->Contact->ContactType->merge($this->data['ContactType'], $new_tags['contact_type_ids']);
            } else {
                $entered_contact_types = array();
            }
            
            # go through entered noserub contact types and decide what to do
            foreach($entered_noserub_contact_types as $id => $marked) {
                if($marked && !in_array($id, $selected_noserub_contact_types)) {
                    $data = array(
                        'ContactsNoserubContactType' => array(
                            'contact_id'              => $contact_id,
                            'noserub_contact_type_id' => $id));
                    $this->Contact->ContactsNoserubContactType->create();        
                    $this->Contact->ContactsNoserubContactType->save($data);
                } else if(!$marked && in_array($id, $selected_noserub_contact_types)) {
                    $sql = 'DELETE FROM ' . $this->Contact->ContactsNoserubContactType->tablePrefix . 'contacts_noserub_contact_types WHERE ' .
                           'contact_id=' . $contact_id . ' AND noserub_contact_type_id=' . $id;
                    $this->Contact->ContactsNoserubContactType->query($sql);
                }
            }
            
            # go through manually entered tags and create them
            foreach($new_tags['tags'] as $tag) {
                if($tag) {
                    $data = array(
                        'ContactType' => array(
                            'identity_id' => Context::loggedInIdentityId(),
                            'name'        => $tag));
                    $this->Contact->ContactType->create();
                    $this->Contact->ContactType->save($data);
                    # add id to entered contact types, so it gets assigned
                    # to that contact
                    $entered_contact_types[$this->Contact->ContactType->id] = 1;
                }
            }
            
            # go through entered contact types and decide what to do
            foreach($entered_contact_types as $id => $marked) {
                if($marked && !in_array($id, $selected_contact_types)) {
                    $data = array(
                        'ContactTypesContact' => array(
                            'contact_id'      => $contact_id,
                            'contact_type_id' => $id));
                    $this->Contact->ContactTypesContact->create();        
                    $this->Contact->ContactTypesContact->save($data);
                } else if(!$marked && in_array($id, $selected_contact_types)) {
                    $sql = 'DELETE FROM ' . $this->Contact->ContactTypesContact->tablePrefix . 'contact_types_contacts WHERE ' .
                           'contact_id=' . $contact_id . ' AND contact_type_id=' . $id;
                    $this->Contact->ContactTypesContact->query($sql);
                    
                    $this->Contact->ContactType->removeIfUnused($id);
                }
            }
                	    
    		$this->flashMessage('success', __('Contact updated.', true));
    		$this->redirect('/contacts/');
    	}
    	
    	$this->set('contact', $contact);
    	$this->set('contact_photo', $this->Contact->Identity->getPhotoUrl($contact, 'WithIdentity'));
    	
    	$this->Contact->NoserubContactType->contain();
	    $this->set('noserub_contact_types', $this->Contact->NoserubContactType->find('all'));	    
	    
	    $this->Contact->ContactType->contain();
	    $this->set('contact_types', $this->Contact->ContactType->findAllByIdentityId(Context::loggedInIdentityId()));
    }
    
    /**
     * Displays the social stream of one identity. That means all his/her contact's activities.
     *
     * @param  
     * @return 
     * @access 
     */
    public function network() {
        $this->checkUnsecure();
        
        $this->render('../identities/network');
    }
    
    public function add_as_contact() {
        $username         = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted         = $this->Contact->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity) {
            # this user is not logged in
            $this->redirect('/');
        }
        
        # make sure, that the correct security token is set
        $this->ensureSecurityToken();
        
        # get id for this username
        $this->Contact->Identity->contain();
        $identity = $this->Contact->Identity->findByUsername($splitted['username'], array('Identity.id'));
        
        if($session_identity['id'] == $identity['Identity']['id']) {
            # this is the logged in user. no reason to allow him to add
            # himself as contact.
            $this->flashMessage('alert', __('You cannot add yourself as a contact.', true));
            $this->redirect('/' . $splitted['local_username']);
        }
        
        if($this->Contact->add($session_identity['id'], $identity['Identity']['id'])) {
            $this->flashMessage('success', __('New contact added.', true));
		    $this->Contact->Identity->Entry->addNewContact($session_identity['id'], $identity['Identity']['id'], null);
	    } else {
		    $this->flashMessage('error', 'Could not add contact');
		}
        
        $this->redirect('/' . $splitted['local_username']);
    }
}