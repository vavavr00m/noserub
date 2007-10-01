<?php
/* SVN FILE: $Id:$ */
 
class ContactsController extends AppController {
    var $uses = array('Contact');
    var $helpers = array('form', 'nicetime');
    var $components = array('cluster');
    
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
        
        $this->Contact->recursive = 1;
        $this->Contact->expects('Contact.Contact', 'Contact.WithIdentity', 'WithIdentity.WithIdentity');
        
        $this->set('data', $this->Contact->findAllByIdentityId($identity['Identity']['id']));
        $this->set('session_identity', $session_identity);
        
        if($session_identity['username'] == $splitted['username']) {
            $this->set('headline', 'Your contacts');
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
                	$this->Contact->invalidate('noserub_id', 'unique');
                    $this->render();
                    exit;
                }
                
                # now create the contact relationship
                $this->Contact->create();
                $contact = array('identity_id'      => $session_identity['id'],
                                 'with_identity_id' => $new_identity_id);
                $saveable = array('identity_id', 'with_identity_id', 'created', 'modified');
                if($this->Contact->save($contact, true, $saveable)) {
                    $this->redirect('/' . $splitted['local_username'] . '/contacts/', null, true);
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
                    # no validation, as we have no password.
                    if($this->Contact->Identity->save($identity, false, $saveable)) {
                        # create the contact now
                        $this->Contact->create();
                        $contact = array('identity_id'      => $session_identity['id'],
                                         'with_identity_id' => $this->Contact->Identity->id);
                        $saveable = array('identity_id', 'with_identity_id', 'created', 'modified');
                        if($this->Contact->save($contact, true, $saveable)) {
                            $this->redirect('/' . $splitted['local_username'] . '/contacts/', null, true);
                        }
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
    function delete($contact_id) {
        $username          = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted          = $this->Contact->Identity->splitUsername($username);
        $session_identity  = $this->Session->read('Identity');
        
        if(!$session_identity || !$username || $splitted['username'] != $session_identity['username']) {
            # this is not the logged in user
            $this->redirect('/' . $session_identity['local_username'] . '/contacts/', null, true);
        }
        
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
    
    /**
     * Method description
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
        
        if(!$session_identity || $splitted['username'] != $session_identity['username']) {
            # this is not the logged in user
            $this->redirect('/', null, true);
        }

        # sanitize filter
        switch($filter) {
            case 'photo':
            case 'video':
            case 'audio':
            case 'link':
            case 'text':
            case 'event':
            case 'micropublish':
                $filter = $filter; 
                break;
            
            default: 
                $filter = false;
        }
        # get all contact identities and their services
        $this->Contact->recursive = 3;
        $this->Contact->expects('Contact.WithIdentity', 
                                'WithIdentity.Account',
                                'Account.Service',
                                'Account.ServiceType');
        $data = $this->Contact->findAllByIdentityId($session_identity['id']);

        $items = array();
        foreach($data as $contact) {
            foreach($contact['WithIdentity']['Account'] as $account) {
                if(!$filter || $account['ServiceType']['token'] == $filter) {
                    if(defined('NOSERUB_USE_FEED_CACHE') && NOSERUB_USE_FEED_CACHE) {
                        $new_items = $this->Contact->Identity->Account->Feed->access($account['id']);
                    } else {
                        $new_items = $this->Contact->Identity->Account->Service->feed2array($contact['WithIdentity']['username'], $account['service_id'], $account['service_type_id'], $account['feed_url']);
                    }
                    if($new_items) {
                        $items = array_merge($items, $new_items);
                    }
                }
            }
        }        

        usort($items, 'sort_items');
        $items = $this->cluster->create($items);
                
        $this->set('data', $items);
        $this->set('filter', $filter);
        $this->set('headline', 'Activities in ' . $splitted['local_username'] . '\'s social network');
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
        
        # get id for this username
        $this->Contact->Identity->recursive = 0;
        $this->Contact->Identity->expects('Identity');
        $identity = $this->Contact->Identity->findByUsername($splitted['username'], array('Identity.id'));
        
        if($session_identity['id'] == $identity['Identity']['id']) {
            # this is the logged in user. no reason to allow him to add
            # himself as contact.
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
        }
        
        $this->redirect('/' . $splitted['local_username'], null, true);
    }
}