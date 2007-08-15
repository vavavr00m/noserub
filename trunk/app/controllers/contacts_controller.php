<?php
/* SVN FILE: $Id:$ */
 
class ContactsController extends AppController {
    var $uses = array('Contact');
    var $helpers = array('form');
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function index() {
        $username    = isset($this->params['username']) ? $this->params['username'] : '';
        $identity_id = $this->Session->read('Identity.id');
        
        if(!$identity_id || !$username || $username != $this->Session->read('Identity.username')) {
            # this is not the logged in user. for the moment, all identities are privat
            $this->redirect('/');
            exit;
        }
        
        $this->Contact->recursive = 1;
        $this->Contact->expects('Contact.Contact', 'Contact.WithIdentity', 'WithIdentity.WithIdentity');
        
        $this->set('data', $this->Contact->findAllByIdentityId($identity_id));
        
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
        $username    = isset($this->params['username']) ? $this->params['username'] : '';
        $identity_id = $this->Session->read('Identity.id');
        
        if(!$identity_id || !$username || $username != $this->Session->read('Identity.username')) {
            # this is not the logged in user
            $this->redirect('/');
            exit;
        }
        
        if($this->data) {
            $this->Contact->data = $this->data;
            if($this->Contact->validates()) {
                # check, wether this should be a local contact or a real noserub contact
                $splitusername = $this->Contact->Identity->splitUsername($this->data['Contact']['username']);
                if(!empty($splitusername['domain'])) {
                    # this is a real noserub contact
                    $identity_username = $this->data['Contact']['username'];
                    # see, if we already have it
                    $identity = $this->Contact->Identity->findByUsername($identity_username);
                    if(!$identity) {
                        $this->Contact->Identity->create();
                        $identity = array('username' => $identity_username);
                        $saveable = array('username');
                        $this->Contact->Identity->save($identity, false, $saveable);
                        $new_identity_id = $this->Contact->Identity->id;
                        
                        # et user data
                        $result = $this->requestAction('/jobs/' . NOSERUB_ADMIN_HASH . '/sync/identity/' . $identity_username . '/');
                        if($result == false) {
                            # user could not be found, so delete it
                            $this->Contact->Identity->delete();
                            $this->Contact->validationErrors['username'] = 'User could not be found at target server!';
                            $this->render();
                            exit;
                        }
                    } else {
                        $new_identity_id = $identity['Identity']['id'];
                    }
                    
                    # now create the contact relationship
                    $this->Contact->create();
                    $contact = array('identity_id'      => $identity_id,
                                     'with_identity_id' => $new_identity_id);
                    $saveable = array('identity_id', 'with_identity_id', 'created', 'modified');
                    if($this->Contact->save($contact, true, $saveable)) {
                        $this->redirect('/' . $username . '/contacts/');
                        exit;
                    }
                } else {
                    # we now need to create a new identity and a new contact
                    # create the username with the special namespace
                    $identity_username = $this->data['Contact']['username'] . ':' . $username . '@' . NOSERUB_DOMAIN;
                    # check, if this is unique
                    $this->Contact->Identity->recursive = 0;
                    $this->Contact->Identity->expects('Contact');
                    if($this->Contact->Identity->findCount(array('username' => $identity_username)) == 0) {
                        $this->Contact->Identity->create();
                        $identity = array('username' => $identity_username);
                        $saveable = array('username');
                        # no validation, as we have no password.
                        if($this->Contact->Identity->save($identity, false, $saveable)) {
                            # create the contact now
                            $this->Contact->create();
                            $contact = array('identity_id'      => $identity_id,
                                             'with_identity_id' => $this->Contact->Identity->id);
                            $saveable = array('identity_id', 'with_identity_id', 'created', 'modified');
                            if($this->Contact->save($contact, true, $saveable)) {
                                $this->redirect('/' . $username . '/contacts/');
                                exit;
                            }
                        }
                    }
                }
            }
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
        $identity_id       = $this->Session->read('Identity.id');
        $identity_username = $this->Session->read('Identity.username');
        
        if(!$identity_id || !$username || $username != $identity_username) {
            # this is not the logged in user
            $this->redirect('/'.$identity_username.'/contacts/');
            exit;
        }
        
        # check, if the contact belongs to the identity
        $this->Contact->recursive = 0;
        $this->Contact->expects('Contact');
        $contact = $this->Contact->find(array('id'          => $contact_id,
                                              'identity_id' => $identity_id));
    
        if(!$contact) {
            # contact not found for logged in user
            $this->redirect('/'.$identity_username.'/contacts/');
            exit;
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
        
        if($with_identity['Identity']['namespace'] == $identity_username) {
            # it's only local, so delete the identity
            $this->Contact->Identity->id = $with_identity_id;
            $this->Contact->Identity->delete();
            
            # now delete the accounts, too
            $this->Contact->Identity->Account->deleteByIdentityId($with_identity_id);
        }

        $this->redirect('/'.$identity_username.'/contacts/');
        exit;
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function network() {
        $filter      = isset($this->params['filter'])   ? $this->params['filter']   : '';
        $username    = isset($this->params['username']) ? $this->params['username'] : '';
        $identity_id = $this->Session->read('Identity.id');
        
        if(!$identity_id || !$username || $username != $this->Session->read('Identity.username')) {
            # this is not the logged in user
            $this->redirect('/');
            exit;
        }

        # sanitize filter
        switch($filter) {
            case 'media':
            case 'link':
            case 'text':
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
        $data = $this->Contact->findAllByIdentityId($identity_id);

        $items = array();
        foreach($data as $contact) {
            foreach($contact['WithIdentity']['Account'] as $account) {
                if(!$filter || $account['ServiceType']['token'] == $filter) {
                    $new_items = $this->Contact->Identity->Account->Service->feed2array($account['service_id'], $account['feed_url']);
                    # add some identity info
                    foreach($new_items as $key => $value) {
                        $new_items[$key]['username'] = $contact['WithIdentity']['username'];
                    }
                    $items = array_merge($items, $new_items);
                }
            }
        }        

        usort($items, 'sort_items');
                
        $this->set('data', $items);
        $this->set('filter', $filter);
    }
}
function sort_items($a, $b) {
	return $a['datetime'] < $b['datetime'];
}