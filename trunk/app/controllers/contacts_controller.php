<?php
/* SVN FILE: $Id:$ */
 
class ContactsController extends AppController {
    public $uses = array('Contact');
    public $helpers = array('nicetime', 'flashmessage', 'xfn');
    public $components = array('cluster', 'api', 'OauthServiceProvider');
    
    public function index() {
        $this->checkUnsecure();
        
        $username         = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted         = $this->Contact->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        $tag_filter = $this->Session->read('Filter.Contact.Tag');
        if(!$tag_filter) {
            $tag_filter = 'all';
        }
        if($this->data) {
            $tag_filter = $this->data['TagFilter']['id'];
            $this->Session->write('Filter.Contact.Tag', $tag_filter);
        }
        $this->data['TagFilter']['id'] = $tag_filter;
        
        # get identity of displayed user
        $this->Contact->Identity->contain();
        $identity = $this->Contact->Identity->findByUsername($splitted['username']);
        if(!$identity) {
            # identity not found
            $this->redirect('/');
        }
        $this->set('identity', $identity['Identity']);
        
        $this->set('tag_filter_list', $this->Contact->getTagList($identity['Identity']['id']));
        
        # get all noserub contacts
        $filter = array('tag' => $tag_filter, 'type' => 'public');
        $this->set('noserub_contacts', $this->Contact->getForDisplay($identity['Identity']['id'], $filter));
       
        # get all private contacts, if this is the logged in user
        if(isset($session_identity['id']) && $splitted['username'] == $session_identity['username']) {
            $filter = array('tag' => $tag_filter, 'type' => 'private');
            $this->set('private_contacts', $this->Contact->getForDisplay($identity['Identity']['id'], $filter));
        }
        
        $this->set('session_identity', $session_identity);
		$this->set('base_url_for_avatars', $this->Contact->Identity->getBaseUrlForAvatars());
        
        if($session_identity['username'] == $splitted['username']) {
            $this->set('headline', __('My contacts', true));
        } else {
            $this->set('headline', sprintf(__("%s's contacts", true), $splitted['username']));
        }
    }
    
    /**
     * adds a new contact to an identity
     * todo: check for existing identities
     */
    public function add() {
        $username         = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted         = $this->Contact->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || !$username || $splitted['username'] != $session_identity['username']) {
            # this is not the logged in user
            $this->redirect('/');
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
                    return;
                }
                
                # check, if this is the logged in user
                if($session_identity['username'] == $identity_username_splitted['username'] ||
                   'www.'.$session_identity['username'] == $identity_username_splitted['username']) {
                    $this->Contact->invalidate('noserub_id', 'own_noserub_id');
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
                if ($this->Contact->hasAny(array('identity_id' => $session_identity['id'], 'with_identity_id' => $new_identity_id))) {
                    $this->Contact->invalidate('noserub_id', 'unique');
                    $this->render();
                    return;
                }
                
                if($this->Contact->add($session_identity['id'], $new_identity_id)) {
                    $this->Contact->Identity->Entry->addNewContact($session_identity['id'], $new_identity_id, null);
                    $this->flashMessage('success', __('New contact added.', true));
    			    $this->Session->write('Contacts.add.Contact.id', $this->Contact->id);
    			    $this->redirect('/' . $splitted['local_username'] . '/contacts/' . $this->Contact->id . '/edit/');
			    } else {
			        $this->flashMessage('error', __('Could not add contact.', true));
			    }
            } else if(isset($this->params['form']['create']) && $this->Contact->validates()) {
                # we now need to create a new identity and a new contact
                # create the username with the special namespace
                $new_identity_username = $this->data['Contact']['username'] . '@' . $splitted['local_username'];
                $new_splitted = $this->Contact->Identity->splitUsername($new_identity_username);
                
                # check, if this is unique
                if(!$this->Contact->Identity->hasAny(array('username' => $new_splitted['username']))) {
                    $this->Contact->Identity->create();
                    $identity = array('is_local' => 1,
                                      'username' => $new_splitted['username']);
                    $saveable = array('is_local', 'username', 'created', 'modified');
                    $this->Contact->Identity->save($identity, true, $saveable);
                    
                    if($this->Contact->add($session_identity['id'], $this->Contact->Identity->id)) {
                        $this->flashMessage('success', __('New contact added.', true));
        			    $this->Session->write('Contacts.add.Contact.id', $this->Contact->id);
        			    $this->redirect('/' . $splitted['local_username'] . '/contacts/' . $this->Contact->id . '/edit/');
    			    } else {
        			    $this->flashMessage('error', 'Could not add contact');
        			}
                } else {
                	$this->Contact->invalidate('username', 'unique');
                    $this->render();
                    return;
                }
            } else {
                # we should never come here, unless the username doesn't validate
            	$this->render();
                return;
            }
        }
        
        if($splitted['username'] == $session_identity['username']) {
            $this->set('headline', __('Add a contact to your social network', true));
        } else {
            $this->set('headline', sprintf(__("Add a contact to %s's social network", true), $splitted['local_username']));
        }
    }
        
    public function delete() {
        $contact_id        = isset($this->params['contact_id']) ? $this->params['contact_id'] : '';
        $username          = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted          = $this->Contact->Identity->splitUsername($username);
        $session_identity  = $this->Session->read('Identity');
        
        if(!$session_identity || !$username || $splitted['username'] != $session_identity['username']) {
            # this is not the logged in user
            $this->redirect('/' . $session_identity['local_username'] . '/contacts/');
        }

        # make sure, that the correct security token is set
        $this->ensureSecurityToken();
        
        # check, if the contact belongs to the identity
        $this->Contact->contain();
        $contact = $this->Contact->find(array('id'          => $contact_id,
                                              'identity_id' => $session_identity['id']));
    
        if(!$contact) {
            # contact not found for logged in user
            $this->redirect('/' . $session_identity['local_username'] . '/contacts/');
        }
        
        $this->Contact->deleteContactTypeAssociations($contact_id);
        $this->Contact->OmbAccessToken->deleteByContactId($contact_id);
        $this->Contact->OmbLocalServiceAccessToken->deleteByContactId($contact_id);
        
        # remove this contact
        $with_identity_id = $contact['Contact']['with_identity_id'];
        $this->Contact->id = $contact_id;
        $this->Contact->delete();
        $this->flashMessage('success', __('Removed the contact.', true));
        
        # get the other identity in order to determine, if
        # this was a local identity and therefore can be deleted
        $this->Contact->Identity->contain();
        $with_identity = $this->Contact->WithIdentity->findById($with_identity_id);
        
        if($with_identity['WithIdentity']['namespace'] == $session_identity['local_username']) {
            # it's only local, so delete the identity
            $this->Contact->Identity->id = $with_identity_id;
            $this->Contact->Identity->delete();
            
            # now delete the accounts, too
            $this->Contact->Identity->Account->deleteByIdentityId($with_identity_id);
        }

        $this->redirect('/' . $session_identity['local_username'] . '/contacts/');
    }
    
    /**
     * Display some information about the contact. Especially for the
     * external contacts, we want to show their accounts and maybe other
     * data we have about that identity.
     */
    public function info() {
        $contact_id = isset($this->params['contact_id']) ? $this->params['contact_id'] : '';
    	$username   = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted   = $this->Contact->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || !$username || $splitted['username'] != $session_identity['username']) {
            # this is not the logged in user
            $this->redirect('/' . $session_identity['local_username'] . '/contacts/');
        }
 
        # get the contact
		$this->Contact->contain('Identity', 'WithIdentity');
	    $contact = $this->Contact->findById($contact_id);
	    
        if($session_identity['id'] != $contact['Contact']['identity_id']) {
            # this is not a contact of the logged in user
            $this->redirect('/' . $session_identity['local_username'] . '/contacts/');
        }
        
        $this->set('contact', $contact);
        $this->set('contact_photo', $this->Contact->Identity->getPhotoUrl($contact, 'WithIdentity'));
        
        # get contact's accounts
        $this->Contact->Identity->Account->contain('Service');
        $this->set('accounts', $this->Contact->Identity->Account->findAllByIdentityId($contact['WithIdentity']['id']));
        
        $this->set('headline', sprintf('Info about %s', $contact['WithIdentity']['username']));
    }
    
    public function edit() {
    	$contact_id = isset($this->params['contact_id']) ? $this->params['contact_id'] : '';
    	$username   = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted   = $this->Contact->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        
        if(!$session_identity || !$username || $splitted['username'] != $session_identity['username']) {
            # this is not the logged in user
            $this->redirect('/' . $session_identity['local_username'] . '/contacts/');
        }
        
        # get the contact
	    $this->Contact->contain(array('Identity', 'WithIdentity', 'ContactType', 'NoserubContactType'));
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
    	    $new_tags = $this->Contact->ContactType->extract($session_identity['id'], $new_tags);
    	    
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
                    $this->Contact->ContactTypesContact->query($sql);
                    
                    $this->Contact->ContactType->removeIfUnused($id);
                }
            }
                	    
    		$this->flashMessage('success', __('Contact updated.', true));
    		$this->redirect('/' . $session_identity['local_username'] . '/contacts/');
    	}
    	
    	$this->set('contact', $contact);
    	$this->set('contact_photo', $this->Contact->Identity->getPhotoUrl($contact, 'WithIdentity'));
    	
    	$this->set('headline', __('Edit the contact details', true));
    	
    	$this->Contact->NoserubContactType->contain();
	    $this->set('noserub_contact_types', $this->Contact->NoserubContactType->find('all'));	    
	    
	    $this->Contact->ContactType->contain();
	    $this->set('contact_types', $this->Contact->ContactType->findAllByIdentityId($session_identity['id']));
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
        
        $session_identity = $this->Session->read('Identity');
        
        if(!isset($this->params['username'])) {
            if(!$session_identity) {
                $this->redirect('/social_stream/');
            } else {
                $username = $session_identity['local_username'];
            }
        } else {
            $username = isset($this->params['username']) ? $this->params['username'] : '';
        }
        
        $filter   = isset($this->params['filter'])   ? $this->params['filter']   : '';
        $splitted = $this->Contact->Identity->splitUsername($username);
        
        if(isset($this->data['Micropublish']['value'])) {
            $this->Contact->Identity->id = $session_identity['id'];
            $frontpage_updates = $this->Contact->Identity->field('frontpage_updates');
            $this->Contact->Identity->Entry->addMicropublish(
                $session_identity['id'], 
                $this->data['Micropublish']['value'],
                $frontpage_updates == 0
            );
            $this->data = array();
        }
        
        $tag_filter = $this->Session->read('Filter.Contact.Tag');
        if(!$tag_filter) {
            $tag_filter = 'all';
        }
        if($this->data) {
            $this->ensureSecurityToken();

            if(isset($this->data['Locator']['id'])) {
                # location was changed
                $location_id = $this->data['Locator']['id'];
                if($location_id == 0 && $this->data['Locator']['name'] != '') {
                    # a new location must be created
                    $data = array('identity_id' => $session_identity['id'],
                                  'name'        => $this->data['Locator']['name']);
                    $this->Contact->Identity->Location->create();
                    $this->Contact->Identity->Location->save($data);
                    $location_id = $this->Contact->Identity->Location->id;
                } 
                if($location_id > 0) {
                    $this->Contact->Identity->Location->setTo($session_identity['id'], $location_id);                
                    $this->flashMessage('success', __('Location updated.', true));
                }
            } else {
                $tag_filter = $this->data['TagFilter']['id'];
                $this->Session->write('Filter.Contact.Tag', $tag_filter);
            }
        }
        $this->data['TagFilter']['id'] = $tag_filter;
        
		$filter = $this->Contact->Identity->Account->ServiceType->sanitizeFilter($filter);
        # get filter
        $show_in_overview = isset($session_identity['overview_filters']) ? explode(',', $session_identity['overview_filters']) : $this->Contact->Identity->Account->ServiceType->getDefaultFilters();
        if($filter == '') {
            # no filter, that means "overview"
            $filter = $show_in_overview;
        } else {
            $filter = array($filter);
        }

        $this->Contact->Identity->contain();
        $about_identity = $this->Contact->Identity->findByUsername($splitted['username']);
        $about_identity = isset($about_identity['Identity']) ? $about_identity['Identity'] : false;
        
        $this->set('tag_filter_list', $this->Contact->getTagList($about_identity['id']));
        
        $is_self = $session_identity && $session_identity['local_username'] == $splitted['local_username'];
        # get (filtered) contacts
        if($is_self) {
            # this is my network, so I can show every contact
            $contact_filter = array('tag' => $tag_filter);
        } else {
            # this is someone elses network, so I show only the noserub contacts
            $contact_filter = array('tag' => $tag_filter, 'type' => 'public');
        }
        $data = $this->Contact->getForDisplay($about_identity['id'], $contact_filter);
        
        # we need to go through all this now and get Accounts and Services
        # also save all contacts
        $contacts = array();
        foreach($data as $key => $value) {
            $contacts[] = $value['WithIdentity'];
        }

        $contact_ids = Set::extract($contacts, '{n}.id');
        # extend it by identity_id of the user that is currently seen
        $contact_ids[] = $about_identity['id'];
        
        # get last 100 items
        $conditions = array(
            'filter'      => $filter,
            'identity_id' => $contact_ids
        );
        $items = $this->Contact->Identity->Entry->getForDisplay($conditions, 50, true);
        if($items) {
            usort($items, 'sort_items');
            $items = $this->cluster->removeDuplicates($items);
            $items = $this->cluster->create($items);
        }
    
        # get list of locations, if this is the logged in user
        if($is_self) {
            $this->set('locations', $this->Contact->Identity->Location->find('list', array('conditions'=>array('identity_id' => $session_identity['id']),'order' => 'name ASC')));
        }
        
        $this->set('items', $items);
        $this->set('identities', $contacts);
        $this->set('filter', $filter);
        $this->set('session_identity', $session_identity);
        $this->set('about_identity', $about_identity);
        $this->set('base_url_for_avatars', $this->Contact->Identity->getBaseUrlForAvatars());
        $this->set('headline', sprintf(__("Activities in %s's contact's social stream", true), $splitted['local_username']));
        
        $this->render('../identities/social_stream');
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
    
    /**
     * returns list of all contacts for this user
     */
    public function api_get() {
    	if (isset($this->params['username'])) {
    		$identity = $this->api->getIdentity();
        	$this->api->exitWith404ErrorIfInvalid($identity);
        	$identity_id = $identity['Identity']['id'];
		} else {
    		$key = $this->OauthServiceProvider->getAccessTokenKeyOrDie();
			$accessToken = ClassRegistry::init('AccessToken');
			$identity_id = $accessToken->field('identity_id', array('token_key' => $key));
		}
    	                                     
        # get all noserub contacts
        $this->Contact->contain(array('WithIdentity', 'NoserubContactType'));
        
        $conditions = array(
            'Contact.identity_id' => $identity_id,
            'WithIdentity.username NOT LIKE "%@%"'
        );
    	$data = $this->Contact->find('all', array('conditions' => $conditions,
    											  'order' => array('WithIdentity.created DESC')));   
        
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
            
            $contact = array(
                'url' => 'http://' . $item['WithIdentity']['username'],
                'firstname' => $item['WithIdentity']['firstname'],
                'lastname'  => $item['WithIdentity']['lastname'],
                'photo'     => $this->Contact->Identity->getPhotoUrl($item, 'WithIdentity', true),
                'xfn'       => join(' ', $xfn)
            );
            $contacts[] = $contact;
        }
        
        $this->set('data', $contacts);
        $this->api->render();
    }
    
    /**
     * contacts of a given identity.
     */
    public function widget_contacts() {
        $session_identity = $this->Session->read('Identity');
        
        $identity_id = $this->params['identity_id'];
        $this->Contact->Identity->contain();
        $this->Contact->Identity->id = $identity_id;
        $this->set('identity', $this->Contact->Identity->read());
        
        $tag_filter = $this->Session->read('Filter.Contact.Tag');
        if(!$tag_filter) {
            $tag_filter = 'all';
        }
        
        $is_self = $session_identity && $session_identity['id'] == $identity_id;
        # get (filtered) contacts
        if($is_self) {
            # this is my network, so I can show every contact
            $contact_filter = array('tag' => $tag_filter);
        } else {
            # this is someone elses network, so I show only the noserub contacts
            $contact_filter = array('tag' => $tag_filter, 'type' => 'public');
        }
        $data = $this->Contact->getForDisplay($identity_id, $contact_filter, 9);
        $contacts = array();
        foreach($data as $key => $value) {
            $contacts[] = $value['WithIdentity'];
        }
        $this->set('data', $contacts);
    }
    
    public function widget_my_contacts() {
        $logged_in_identity_id = $this->Session->read('Identity.id');
        if(!$logged_in_identity_id) {
            return false;
        }
        
        # get contacts of the displayed profile
        $all_contacts = $this->Contact->getForIdentity($logged_in_identity_id);

        $contacts = array();
        $num_private_contacts = 0;
        $num_noserub_contacts = 0;
        foreach($all_contacts as $contact) {
            if(strpos($contact['WithIdentity']['username'], '@') === false) {
                $num_noserub_contacts++;
                if(count($contacts) < 9) {
                    $contacts[] = $contact;
                }
            } else {
                $num_private_contacts++;
                if(count($contacts) < 9) {
                    $contacts[] = $contact;
                }
            }
        }
        $this->set('num_private_contacts', $num_private_contacts);
        $this->set('num_noserub_contacts', $num_noserub_contacts);
        $this->set('data', $contacts);
    }
}