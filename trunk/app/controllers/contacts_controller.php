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
                            $this->redirect('/noserub/' . $username . '/contacts/');
                            exit;
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
            case 'photo':
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
                                'Account.Service');
        $data = $this->Contact->findAllByIdentityId($identity_id);

        $items = array();
        foreach($data as $contact) {
            foreach($contact['WithIdentity']['Account'] as $account) {
                if(!$filter || $account['Service']['type'] == $filter) {
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