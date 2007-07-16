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
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        if($username && $username != $this->Session->read('Identity.username')) {
            # this is not the logged in user. for the moment, all identities are privat
            $this->redirect('/');
            exit;
        }
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
                $this->Session->write('Identity.id',       $identity['Identity']['id']);
                $this->Session->write('Identity.username', $identity['Identity']['username']);
                $this->redirect('/noserub/' . urlencode(strtolower($identity['Identity']['username'])) . '/');
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