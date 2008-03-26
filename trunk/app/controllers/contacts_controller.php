<?php
/* SVN FILE: $Id:$ */
 
class ContactsController extends AppController {
    var $uses = array('Contact');
    var $helpers = array('form', 'nicetime', 'flashmessage', 'xfn');
    var $components = array('cluster', 'api');
    
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
        $this->Contact->expects('Contact.Contact', 'Contact.WithIdentity', 'WithIdentity.WithIdentity', 'ContactType.ContactType', 'NoserubContactType.NoserubContactType');
        
        $this->set('noserub_contacts', $this->Contact->findAll(array('Contact.identity_id' => $identity['Identity']['id'],
                                                                     'WithIdentity.username NOT LIKE "%@%"'), 
                                                               null, 
                                                               'WithIdentity.username ASC'));
        # get all private contacts, if this is the logged in user
        if(isset($session_identity['id']) && $splitted['username'] == $session_identity['username']) {
            $this->Contact->recursive = 1;
            $this->Contact->expects('Contact.Contact', 'Contact.WithIdentity', 'WithIdentity.WithIdentity', 'ContactType.ContactType', 'NoserubContactType.NoserubContactType');

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
                $identity_username_splitted = $this->Contact->Identity->splitUsername($identity_username, false);

                # so, check, if this is really the case
                if($identity_username === '') {
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
                
                if($this->Contact->add($session_identity['id'], $new_identity_id)) {
                    $this->flashMessage('success', 'New contact added.');
    			    $this->Session->write('Contacts.add.Contact.id', $this->Contact->id);
    			    $this->redirect('/' . $splitted['local_username'] . '/contacts/' . $this->Contact->id . '/edit/');
			    } else {
			        $this->flashMessage('error', 'Could not add contact');
			    }
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
                    
                    if($this->Contact->add($session_identity['id'], $this->Contact->Identity->id)) {
                        $this->flashMessage('success', 'New contact added.');
        			    $this->Session->write('Contacts.add.Contact.id', $this->Contact->id);
        			    $this->redirect('/' . $splitted['local_username'] . '/contacts/' . $this->Contact->id . '/edit/');
    			    } else {
        			    $this->flashMessage('error', 'Could not add contact');
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
        
        # remove contact_type relationships
        $sql = 'DELETE FROM ' . $this->Contact->ContactTypesContact->tablePrefix . 'contact_types_contacts WHERE contact_id=' . $contact_id;
        $this->Contact->ContactTypesContact->execute($sql);
        $sql = 'DELETE FROM ' . $this->Contact->ContactsNoserubContactType->tablePrefix . 'contacts_noserub_contact_types WHERE contact_id=' . $contact_id;
        $this->Contact->ContactsNoserubContactType->execute($sql);
        
        # remove this contact
        $with_identity_id = $contact['Contact']['with_identity_id'];
        $this->Contact->id = $contact_id;
        $this->Contact->delete();
        $this->flashMessage('success', 'Removed the contact.');
        
        # get the other identity in order to determine, if
        # this was a local identity and therefore can be deleted
        $this->Contact->Identity->recursive = 0;
        $this->Contact->Identity->expects('Identity');
        $with_identity = $this->Contact->WithIdentity->findById($with_identity_id);
        
        if($with_identity['WithIdentity']['namespace'] == $session_identity['local_username']) {
            # it's only local, so delete the identity
            $this->Contact->Identity->id = $with_identity_id;
            $this->Contact->Identity->delete();
            
            # now delete the accounts, too
            $this->Contact->Identity->Account->deleteByIdentityId($with_identity_id);
        }

        $this->redirect('/' . $session_identity['local_username'] . '/contacts/', null, true);
    }
    
    function edit() {
    	$contact_id = isset($this->params['contact_id']) ? $this->params['contact_id'] : '';
    	$username   = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted   = $this->Contact->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || !$username || $splitted['username'] != $session_identity['username']) {
            # this is not the logged in user
            $this->redirect('/' . $session_identity['local_username'] . '/contacts/');
        }
        
        # get the contact
        $this->Contact->recursive = 2;
	    $this->Contact->expects('Contact.Contact', 'Identity.Identity', 'WithIdentity.WithIdentity', 'ContactType.ContactType', 'NoserubContactType.NoserubContactType');
	    $contact = $this->Contact->findById($contact_id);
	    
        if($session_identity['id'] != $contact['Contact']['identity_id']) {
            # this is not a contact of the logged in user
            $this->redirect('/' . $session_identity['local_username'] . '/contacts/');
        }
    	
    	# get currently selected types (pre- and user-defined)
    	$selected_contact_types = Set::extract($contact['ContactType'], '{n}.id');
	    $this->set('selected_contact_types', Set::extract($contact['ContactType'], '{n}.id'));
	    $selected_noserub_contact_types = Set::extract($contact['NoserubContactType'], '{n}.id');
	    $this->set('selected_noserub_contact_types', $selected_noserub_contact_types);
	    
    	if($this->data) {
    	    #pr($this->data); exit;
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
    	    $new_tags = $this->Contact->ContactType->extract($new_tags);
    	    
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
                    $this->Contact->ContactsNoserubContactType->execute($sql);
                }
            }
            
            # go through manually entered tags and create them
            foreach($new_tags['tags'] as $tag) {
                if($tag) {
                    $data = array(
                        'ContactType' => array(
                            'identity_id' => $session_identity['id'],
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
                    $this->Contact->ContactTypesContact->execute($sql);
                    
                    $this->Contact->ContactType->removeIfUnused($id);
                }
            }
                	    
    		$this->flashMessage('success', 'Contact updated.');
    		$this->redirect('/' . $session_identity['local_username'] . '/contacts/');
    	}
    	
    	$this->set('contact', $contact);
    	
    	$this->set('headline', 'Edit the contact details');
    	
    	$this->Contact->NoserubContactType->recursive = 0;
    	$this->Contact->NoserubContactType->expects('NoserubContactType');
	    $this->set('noserub_contact_types', $this->Contact->NoserubContactType->findAll());	    
	    
	    $this->Contact->ContactType->recursive = 0;
	    $this->Contact->ContactType->expects('ContactType');
	    $this->set('contact_types', $this->Contact->ContactType->findAllByIdentityId($session_identity['id']));
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
        
		$filter = $this->Contact->Identity->Account->ServiceType->sanitizeFilter($filter);
        # get filter
        $show_in_overview = isset($session_identity['overview_filters']) ? explode(',', $session_identity['overview_filters']) : $this->Contact->Identity->Account->ServiceType->getDefaultFilters();
        if($filter == '') {
            # no filter, that means "overview"
            $filter = $show_in_overview;
        } else {
            $filter = array($filter);
        }
            
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
                if(in_array($account['ServiceType']['token'], $filter)) {
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
        
        $this->redirect('/' . $splitted['local_username']);
    }
    
    /**
     * returns list of all contacts for this user
     */
    public function api_get() {
        $identity = $this->api->getIdentity();
        $this->api->exitWith404ErrorIfInvalid($identity);

        $sex = array(
            'img' => array(
                0 => Router::url('/images/profile/avatar/noinfo.gif'),
                1 => Router::url('/images/profile/avatar/female.gif'),
                2 => Router::url('/images/profile/avatar/male.gif')
            ),
            'img-small' => array(
                0 => Router::url('/images/profile/avatar/noinfo-small.gif'),
                1 => Router::url('/images/profile/avatar/female-small.gif'),
                2 => Router::url('/images/profile/avatar/male-small.gif')
            )
        );
        
        if(defined('NOSERUB_USE_CDN') && NOSERUB_USE_CDN) {
            $static_base_url = 'http://s3.amazonaws.com/' . NOSERUB_CDN_S3_BUCKET . '/avatars/';
        } else {
            $static_base_url = FULL_BASE_URL . Router::url('/static/avatars/');
        }

                                     
        # get all noserub contacts
        $this->Contact->recursive = 1;
        $this->Contact->expects(
            'Contact.Contact', 
            'Contact.WithIdentity', 
            'WithIdentity.WithIdentity', 
            'NoserubContactType.NoserubContactType'
        );
        
        $conditions = array(
            'Contact.identity_id' => $identity['Identity']['id'],
            'WithIdentity.username NOT LIKE "%@%"'
        );
        
        $data = $this->Contact->findAll($conditions, null, 'WithIdentity.last_activity DESC');
        
        $contacts = array();
        foreach($data as $item) {
            $xfn = array();
            foreach($item['NoserubContactType'] as $nct) {
                if($nct['is_xfn']) {
                    $xfn[] = $nct['name'];
                }
            }
            if(!$xfn) {
                $xfn[] = 'contact';
            }
            
            if($item['WithIdentity']['photo']) {
                if(strpos($item['WithIdentity']['photo'], 'http://') === 0 ||
                   strpos($item['WithIdentity']['photo'], 'https://') === 0) {
                       # contains a complete path, eg. from not local identities
                       $profile_photo = $item['WithIdentity']['photo'];
                   } else {
                       $profile_photo = $static_base_url . $item['WithIdentity']['photo'] . '.jpg';
                   }
            } else {
                $profile_photo = $sex['img-small'][$item['WithIdentity']['sex']];
            }
            
            $contact = array(
                'url' => 'http://' . $item['WithIdentity']['username'],
                'firstname' => $item['WithIdentity']['firstname'],
                'lastname'  => $item['WithIdentity']['lastname'],
                'photo'     => $profile_photo,
                'xfn'       => join(' ', $xfn)
            );
            $contacts[] = $contact;
        }
        
        $this->set('data', $contacts);
        $this->api->render();
    }
}