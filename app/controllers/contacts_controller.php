<?php
/* SVN FILE: $Id:$ */
 
class ContactsController extends AppController {
    var $uses = array('Contact');
    var $helpers = array('form', 'nicetime', 'flashmessage');
    var $components = array('cluster', 'filterSanitize');
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function index() {
        $username         = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted         = $this->Contact->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        # get identity of displayed user
        $this->Contact->Identity->recursive = 0;
        $this->Contact->Identity->expects('Identity');
        $identity = $this->Contact->Identity->findByUsername($splitted['username']);
        if(!$identity) {
            # identity not found
            $this->redirect('/', null, true);
        }
        $this->set('identity', $identity['Identity']);
        
        # get all noserub contacts
        $this->Contact->recursive = 1;
        $this->Contact->expects('Contact.Contact', 'Contact.WithIdentity', 'WithIdentity.WithIdentity');
        
        $this->set('noserub_contacts', $this->Contact->findAll(array('Contact.identity_id' => $identity['Identity']['id'],
                                                                     'WithIdentity.username NOT LIKE "%@%"'), 
                                                               null, 
                                                               'WithIdentity.username ASC'));
        # get all private contacts, if this is the logged in user
        if(isset($session_identity['id']) && $splitted['username'] == $session_identity['username']) {
            $this->Contact->recursive = 1;
            $this->Contact->expects('Contact.Contact', 'Contact.WithIdentity', 'WithIdentity.WithIdentity');

            $this->set('private_contacts', $this->Contact->findAll(array('Contact.identity_id' => $identity['Identity']['id'],
                                                                         'WithIdentity.username LIKE "%@%"'), 
                                                                         null, 
                                                                         'WithIdentity.username ASC'));
        }
        
        $this->set('session_identity', $session_identity);
        
        if($session_identity['username'] == $splitted['username']) {
            $this->set('headline', 'My contacts');
        } else {
            $this->set('headline', $splitted['username'] . '\'s contacts');
        }
    }
    
    /**
     * adds a new contact to an identity
     * todo: check for existing identities
     *
     * @param  
     * @return 
     * @access 
     */
    function add() {
        $username         = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted         = $this->Contact->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || !$username || $splitted['username'] != $session_identity['username']) {
            # this is not the logged in user
            $this->redirect('/', null, true);
        }
        
        if($this->data) {
            $this->Contact->data = $this->data;
            # check, wether this should be a local contact or a real noserub contact
            if(isset($this->params['form']['add'])) {
                # this is a contact with a NoseRub-ID
                $identity_username = trim($this->data['Contact']['noserub_id']);
                $identity_username_splitted = $this->Contact->Identity->splitUsername($identity_username);
                # so, check, if this is really the case
                if(strpos($identity_username_splitted['username'], '/') === false || $identity_username === '') {
                    $this->Contact->invalidate('noserub_id', 'no_valid_noserub_id');
                    $this->render();
                    exit;
                }
                
                # check, if this is the logged in user
                if($session_identity['username'] == $identity_username_splitted['username'] ||
                   'www.'.$session_identity['username'] == $identity_username_splitted['username']) {
                    $this->Contact->invalidate('noserub_id', 'own_noserub_id');
                    $this->render();
                    exit;       
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
                    $result = $this->requestAction('/jobs/' . NOSERUB_ADMIN_HASH . '/sync/identity/' . $new_identity_id . '/');
                    if($result == false) {
                        # user could not be found, so delete it
                        $this->Contact->Identity->delete();
                        $this->Contact->invalidate('noserub_id', 'user_not_found');
                        $this->render();
                        exit;
                    }
                } else {
                    # it's already there, so we can go ahead and add it
                	$new_identity_id = $identity['Identity']['id'];                	
                }
                
                # now create the contact relationship
                
                # but first make sure, that this connection is not already there
                $this->Contact->recursive = 0;
                $this->Contact->expects('Contact');
                $num_of_contacts = $this->Contact->findCount(array('identity_id'      => $session_identity['id'],
                                                                   'with_identity_id' => $new_identity_id));
                if($num_of_contacts > 0) {
                    $this->Contact->invalidate('noserub_id', 'unique');
                    $this->render();
                    exit;
                }
                
                $contact = array('identity_id'      => $session_identity['id'],
                                 'with_identity_id' => $new_identity_id);
                $this->saveContactAndRedirect($contact, $splitted['local_username']);
            } else if(isset($this->params['form']['create']) && $this->Contact->validates()) {
                # we now need to create a new identity and a new contact
                # create the username with the special namespace
                $new_identity_username = $this->data['Contact']['username'] . '@' . $splitted['local_username'];
                $new_splitted = $this->Contact->Identity->splitUsername($new_identity_username);
                
                # check, if this is unique
                $this->Contact->Identity->recursive = 0;
                $this->Contact->Identity->expects('Contact');
                if($this->Contact->Identity->findCount(array('username' => $new_splitted['username'])) == 0) {
                    $this->Contact->Identity->create();
                    $identity = array('is_local' => 1,
                                      'username' => $new_splitted['username']);
                    $saveable = array('is_local', 'username', 'created', 'modified');
                    # no validation, as we have no password.
                    if($this->Contact->Identity->save($identity, false, $saveable)) {
                        $contact = array('identity_id'      => $session_identity['id'],
                                         'with_identity_id' => $this->Contact->Identity->id);
                        $this->saveContactAndRedirect($contact, $splitted['local_username']);
                    }
                } else {
                	$this->Contact->invalidate('username', 'unique');
                    $this->render();
                    exit;
                }
            } else {
                # we should never come here, unless the username doesn't validate
            	$this->render();
                exit;
            }
        }
        
        if($splitted['username'] == $session_identity['username']) {
            $this->set('headline', 'Add a contact to your social network');
        } else {
            $this->set('headline', 'Add a contact to '. $splitted['local_username'] . '\'s social network');
        }
    }
    
    function define_contact_types() {
    	$username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Contact->Identity->splitUsername($username);

    	if ($this->data) {
    		if (isset($this->params['form']['submit'])) { 
    			$contactId = $this->Session->read('Contacts.add.Contact.id');
    			$this->Contact->createAssociationsToNoserubContactTypes($contactId, $this->data['NoserubContactType']);
    			$this->Session->delete('Contacts.add.Contact.id');
    		}
			$this->redirect('/' . $splitted['local_username'] . '/contacts/');
    	} else {
    		$this->set('headline', 'Define contact types');
    		$this->set('noserubContactTypes', $this->Contact->NoserubContactType->findAll());
    	}
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function delete() {
        $contact_id        = isset($this->params['contact_id']) ? $this->params['contact_id'] : '';
        $username          = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted          = $this->Contact->Identity->splitUsername($username);
        $session_identity  = $this->Session->read('Identity');
        
        if(!$session_identity || !$username || $splitted['username'] != $session_identity['username']) {
            # this is not the logged in user
            $this->redirect('/' . $session_identity['local_username'] . '/contacts/', null, true);
        }

        # make sure, that the correct security token is set
        $this->ensureSecurityToken();
        
        # check, if the contact belongs to the identity
        $this->Contact->recursive = 0;
        $this->Contact->expects('Contact');
        $contact = $this->Contact->find(array('id'          => $contact_id,
                                              'identity_id' => $session_identity['id']));
    
        if(!$contact) {
            # contact not found for logged in user
            $this->redirect('/' . $session_identity['local_username'] . '/contacts/', null, true);
        }
        
        # remove this contact
        $with_identity_id = $contact['Contact']['with_identity_id'];
        $this->Contact->id = $contact_id;
        $this->Contact->delete();
        $this->flashMessage('success', 'Removed the contact.');
        
        # get the other identity in order to determine, if
        # this was a local identity and therfore can be deleted
        $this->Contact->Identity->recursive = 0;
        $this->Contact->Identity->expects('Identity');
        $with_identity = $this->Contact->WithIdentity->findById($with_identity_id);
        
        if($with_identity['Identity']['namespace'] == $session_identity['local_username']) {
            # it's only local, so delete the identity
            $this->Contact->Identity->id = $with_identity_id;
            $this->Contact->Identity->delete();
            
            # now delete the accounts, too
            $this->Contact->Identity->Account->deleteByIdentityId($with_identity_id);
        }

        $this->redirect('/' . $session_identity['local_username'] . '/contacts/', null, true);
    }
    
    function edit() {
    	$contactId = isset($this->params['contact_id']) ? $this->params['contact_id'] : '';
    	$this->set('headline', 'Edit contact');
    	$this->set('noserubContactTypes', $this->Contact->NoserubContactType->findAll());
    	// FIXME refactor this into a model function
    	$this->set('selectedNoserubContactTypes', Set::extract($this->Contact->ContactsNoserubContactType->findAllByContactId($contactId), '{n}.ContactsNoserubContactType.noserub_contact_type_id'));
    }
    
    /**
     * Displays the social stream of one identity. That means all his/her contact's activities.
     *
     * @param  
     * @return 
     * @access 
     */
    function network() {
        $filter           = isset($this->params['filter'])   ? $this->params['filter']   : '';
        $username         = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted         = $this->Contact->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
		$filter = $this->filterSanitize->sanitize($filter);
        
        $this->Contact->Identity->recursive = 0;
        $this->Contact->Identity->expects('Identity');
        $about_identity = $this->Contact->Identity->findByUsername($splitted['username']);
        $about_identity = isset($about_identity['Identity']) ? $about_identity['Identity'] : false;
        
        # get all contacts
        $this->Contact->recursive = 1;
        $this->Contact->expects('Contact', 'Contact.WithIdentity');
        if($session_identity && $session_identity['local_username'] == $splitted['local_username']) {
            # this is my network, so I can show every contact
            $data = $this->Contact->findAllByIdentityId($session_identity['id']);
        } else {
            # this is someone elses network, so I show only the noserub contacts
            $data = $this->Contact->findAll(array('Contact.identity_id' => $about_identity['id'],
                                                  'WithIdentity.username NOT LIKE "%@%"'));
        }

        # we need to go through all this now and get Accounts and Services
        # also save all contacts
        $contacts = array();
        foreach($data as $key => $value) {
            $contacts[] = $value['WithIdentity'];
            $this->Contact->Identity->Account->recursive = 1;
            $this->Contact->Identity->Account->expects('Account.Acount', 'Account.Service', 'Account.ServiceType');
            $accounts = $this->Contact->Identity->Account->findAllByIdentityId($value['WithIdentity']['id']);
            $data[$key]['WithIdentity']['Account'] = $accounts;
        }

        $items = array();
        foreach($data as $contact) {
            foreach($contact['WithIdentity']['Account'] as $account) {
                if(!$filter || $account['ServiceType']['token'] == $filter) {
                    if(defined('NOSERUB_USE_FEED_CACHE') && NOSERUB_USE_FEED_CACHE) {
                        $new_items = $this->Contact->Identity->Account->Feed->access($account['Account']['id']);
                    } else {
                        $new_items = $this->Contact->Identity->Account->Service->feed2array($contact['WithIdentity']['username'], $account['Account']['service_id'], $account['Account']['service_type_id'], $account['Account']['feed_url']);
                    }
                    if($new_items) {
                        $items = array_merge($items, $new_items);
                    }
                }
            }
        }        

        usort($items, 'sort_items');
        $items = $this->cluster->create($items);
                
        $this->set('items', $items);
        $this->set('identities', $contacts);
        $this->set('filter', $filter);
        $this->set('about_identity', $about_identity);
        $this->set('headline', 'Activities in ' . $splitted['local_username'] . '\'s contact\'s social stream');
        
        $this->render('../identities/social_stream');
        exit;
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function add_as_contact() {
        $username         = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted         = $this->Contact->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity) {
            # this user is not logged in
            $this->redirect('/', null, true);
        }
        
        # make sure, that the correct security token is set
        $this->ensureSecurityToken();
        
        # get id for this username
        $this->Contact->Identity->recursive = 0;
        $this->Contact->Identity->expects('Identity');
        $identity = $this->Contact->Identity->findByUsername($splitted['username'], array('Identity.id'));
        
        if($session_identity['id'] == $identity['Identity']['id']) {
            # this is the logged in user. no reason to allow him to add
            # himself as contact.
            $this->flashMessage('alert', 'You cannot add yourself as a contact.');
            $this->redirect('/' . $splitted['local_username'], null, true);
        }
        
        # test, if there isn't already a contact
        $this->Contact->recursive = 0;
        $this->Contact->expects('Contact');
        $contacts = $this->Contact->findCount(array('identity_id'      => $session_identity['id'],
                                                    'with_identity_id' => $identity['Identity']['id']));
                                           
        if($contacts == 0) {
            # now create the contact relationship
            $this->Contact->create();
            $contact = array('identity_id'      => $session_identity['id'],
                             'with_identity_id' => $identity['Identity']['id']);
            $saveable = array('identity_id', 'with_identity_id', 'created', 'modified');
            $this->Contact->save($contact, true, $saveable);
            $this->flashMessage('success', 'Added new contact.');
        }
        
        $this->redirect('/' . $splitted['local_username'], null, true);
    }
    
    private function saveContactAndRedirect($contactData, $localUsername) {
		$this->Contact->create();

		$saveable = array('identity_id', 'with_identity_id', 'created', 'modified');
		if($this->Contact->save($contactData, true, $saveable)) {
			$this->flashMessage('success', 'Added new contact.');
			$this->Session->write('Contacts.add.Contact.id', $this->Contact->id);
			$this->redirect('/' . $localUsername . '/contacts/define_contact_types', null, true);
		}
    }
}