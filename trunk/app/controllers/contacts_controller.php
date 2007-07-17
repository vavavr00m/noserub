<?php
/* SVN FILE: $Id:$ */
 
class ContactsController extends AppController {
    var $uses = array('Contact');
    var $helpers = array('form');
    
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
                $identity_username = $this->data['Contact']['username'] . '@' . $username;
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
                            $this->redirect('/noserub/' . $username . '/');
                            exit;
                        }
                    }
                }
            }
        }
    }
}