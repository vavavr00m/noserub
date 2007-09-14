<?php
/* SVN FILE: $Id:$ */
 
class IdentitiesController extends AppController {
    var $uses = array('Identity');
    var $helpers = array('form', 'openid');
    var $components = array('geocoder');
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function index() {
        $filter   = isset($this->params['filter'])   ? $this->params['filter']   : '';
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $username = $splitted['username'];
        
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
        
        $session_identity = $this->Session->read('Identity');
        
        if($splitted['namespace'] !== '' && $splitted['namespace'] != $session_identity['local_username']) {
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
            $data = $this->Identity->find(array('username'  => $username,
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
        
            if($splitted['username'] == $session_identity['username']) {
                $this->set('headline', 'My NoseRub page');
            } else {
                $this->set('headline', $splitted['local_username'] . '\'s NoseRub page');
            }
        } else {
            $this->set('headline', 'Username could not be found!');
        }

        # get all items for those accounts
        $items = array();
        if(is_array($data['Account'])) {
            foreach($data['Account'] as $account) {
                if(!$filter || $account['ServiceType']['token'] == $filter) {
                    $new_items = $this->Identity->Account->Service->feed2array($account['service_id'], $account['feed_url'], 5, false);
                    if($new_items) {
                        # add some identity info
                        foreach($new_items as $key => $value) {
                            $new_items[$key]['username'] = $username;
                        }
                        $items = array_merge($items, $new_items);
                    }
                }
            }
            usort($items, 'sort_items');
        }
    
        $this->set('data', $data);
        $this->set('items', $items);
        $this->set('session_identity', $session_identity);
        $this->set('filter', $filter);
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function settings() {
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        if(!$session_identity || $session_identity['username'] != $splitted['username']) {
            $this->redirect('/', null, true);
        }
        
        if($this->data) {
            # geocode the address
            $geolocation = $this->geocoder->get($this->data['Identity']['address']);
            if($geolocation !== false) {
                $this->data['Identity']['latitude']  = $geolocation['latitude'];
                $this->data['Identity']['longitude'] = $geolocation['longitude'];
            } else {
                $this->data['Identity']['latitude']  = 0;
                $this->data['Identity']['longitude'] = 0;
            }
            
            $saveable = array('firstname', 'lastname', 'sex', 'address', 'latitude', 'longitude', 'modified');
            $this->Identity->id = $session_identity['id'];
            $this->Identity->save($this->data, false, $saveable);
        } else {
            $this->Identity->recursive = 0;
            $this->Identity->expects('Identity');
            $this->data = $this->Identity->findById($session_identity['id']);
        }
        
        $this->set('headline', 'Settings for your NoseRub page');
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
                $this->Session->write('Identity', $identity['Identity']);
                $this->redirect('/' . urlencode(strtolower($identity['Identity']['local_username'])) . '/', null, true);
            } else {
                $this->set('form_error', 'Login not possible');
            }
        }
        
        $this->set('headline', 'Login with existing NoseRub account');
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

        $this->set('headline', 'Register a new NoseRub account');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function verify() {
        $hash = isset($this->params['hash']) ? $this->params['hash'] : '';
        
        $this->set('verify_ok', $this->Identity->verify($hash));
        
        $this->set('headline', 'Verify your e-mail address');
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function register_thanks() {
        $this->set('headline', 'Thanks for your registration!');
    }
}