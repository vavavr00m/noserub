<?php
/* SVN FILE: $Id:$ */
 
class IdentitiesController extends AppController {
    var $uses = array('Identity');
    var $helpers = array('form');
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function index() {
        $filter        = isset($this->params['filter'])   ? $this->params['filter']   : '';
        $full_username = isset($this->params['username']) ? $this->params['username'] : '';
        $namespace     = '';
        
        # sanitize filter
        switch($filter) {
            case 'media':
            case 'link':
            case 'text':
            case 'event':
            case 'micropublish':
                $filter = $filter; 
                break;
            
            default: 
                $filter = false;
        }
        
        if(strpos($full_username, '@') !== false) {
            # this is a username with a namespace: <username>@<namespace>
            $username_namespace = split('@', $full_username);
            $namespace = $username_namespace[1];
        }
        $session_identity_id       = $this->Session->read('Identity.id');
        $session_identity_username = $this->Session->read('Identity.username');
        
        if($namespace !== '' && $namespace != $session_identity_username) {
            # don't display local contacts to anyone else, but the owner
            $data = null;
        } else {
            $this->Identity->recursive = 2;
            $this->Identity->expects('Identity.Identity', 'Identity.Account', 'Identity.Contact',
                                     'Account.Account', 'Account.Service', 'Account.ServiceType',
                                     'Service.Service',
                                     'ServiceType.ServiceType',
                                     'Contact.Contact', 'Contact.WithIdentity',
                                     'WithIdentity.WithIdentity');
            $data = $this->Identity->find(array('username'  => $full_username,
                                                'is_local'  => 1));
            
            # create $about_identity for the view
            $this->set('about_identity', $data['Identity']);
        }
        
        if($data) {
            # expand all identities (domain, namespace, url)
            $data['Identity'] = array_merge($data['Identity'], $this->Identity->splitUsername($data['Identity']['username']));
            foreach($data['Contact'] as $key => $contact) {
                $data['Contact'][$key]['WithIdentity'] = array_merge($contact['WithIdentity'], $this->Identity->splitUsername($contact['WithIdentity']['username']));
            }
        }

        # get all items for those accounts
        $items = array();
        if(is_array($data['Account'])) {
            foreach($data['Account'] as $account) {
                if(!$filter || $account['ServiceType']['token'] == $filter) {
                    $new_items = $this->Identity->Account->Service->feed2array($account['service_id'], $account['feed_url']);
                    if($new_items) {
                        # add some identity info
                        foreach($new_items as $key => $value) {
                            $new_items[$key]['username'] = $full_username;
                        }
                        $items = array_merge($items, $new_items);
                    }
                }
            }
            usort($items, 'sort_items');
        }
    
        $this->set('data', $data);
        $this->set('items', $items);
        $this->set('session_identity_id',       isset($session_identity_id)       ? $session_identity_id       : 0);
        $this->set('session_identity_username', isset($session_identity_username) ? $session_identity_username : '');
        $this->set('url_username', $full_username);
        $this->set('namespace', $namespace);
        $this->set('filter', $filter);
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function login() {
        $this->checkSecure();
        
        if(!empty($this->data)) {
            $identity = $this->Identity->check($this->data);
            if($identity) {
                $this->Session->write('Identity.id',       $identity['Identity']['id']);
                $this->Session->write('Identity.username', $identity['Identity']['username']);
                $this->redirect('/' . urlencode(strtolower($identity['Identity']['username'])) . '/', null, true);
            } else {
                $this->set('form_error', 'Login not possible');
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
    function logout() {
        $this->Session->delete('Identity');
        $this->redirect('/');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function register() {
        $this->checkSecure();
        
        if(NOSERUB_REGISTRATION_TYPE != 'all') {
            $this->redirect('/', null, true);
        }

        if(!empty($this->data)) {
            if($this->Identity->register($this->data)) {
                $this->redirect('/pages/register/thanks/', null, true);
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
    function verify() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $hash     = isset($this->params['hash'])     ? $this->params['hash']     : '';
        
        $this->set('verify_ok', $this->Identity->verify($username, $hash));
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function register_thanks() {
    }
}