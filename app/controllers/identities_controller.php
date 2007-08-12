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
    function sandbox() {
        #$this->Identity->parseNoseRubPage('http://noserub/noserub/dirk.olbertz/');
        $result = $this->Identity->parseNoseRubPage('http://noserub/noserub/timo.derstappen/');
        echo '<pre>'; print_r($result); echo '</pre>';
        exit;
    }
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function index() {
        $namespace = '';
        $username  = isset($this->params['username']) ? $this->params['username'] : '';
        if(strpos($username, '@') !== false) {
            # this is a username with a namespace: <username>@<namespace>
            # we need to change that to the internal name: <username>:<namespace>@<domain>
            $username_namespace = split('@', $username);
            $username  = join(':', $username_namespace);
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
                                     'Account.Account', 'Account.Service',
                                     'Service.Service',
                                     'Contact.Contact', 'Contact.WithIdentity',
                                     'WithIdentity.WithIdentity');
            $data = $this->Identity->findByUsername($username . '@' . NOSERUB_DOMAIN);
        }
        
        if($data) {
            # expand all identities (domain, namespace, url)
            $data['Identity'] = array_merge($data['Identity'], $this->Identity->splitUsername($data['Identity']['username']));
            foreach($data['Contact'] as $key => $contact) {
                $data['Contact'][$key]['WithIdentity'] = array_merge($contact['WithIdentity'], $this->Identity->splitUsername($contact['WithIdentity']['username']));
            }
        }

        $this->set('data', $data);
        $this->set('session_identity_id',       isset($session_identity_id)       ? $session_identity_id       : 0);
        $this->set('session_identity_username', isset($session_identity_username) ? $session_identity_username : '');
        $this->set('namespace', $namespace);
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function login() {
        if(!empty($this->data)) {
            $identity = $this->Identity->check($this->data);
            if($identity) {
                $username = $this->Identity->splitUsername($identity['Identity']['username']);
                $this->Session->write('Identity.id',       $identity['Identity']['id']);
                $this->Session->write('Identity.username', $username['username']);
                $this->redirect('/noserub/' . urlencode(strtolower($username['username'])) . '/');
                exit;
            } else {
                $this->set('form_error', 'Login nicht möglich');
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
        exit;
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function register() {
        if(NOSERUB_REGISTRATION_TYPE != 'all') {
            $this->redirect('/');
            exit;
        }

        if(!empty($this->data)) {
            if($this->Identity->register($this->data)) {
                $this->redirect('/register/thanks/');
                exit;
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