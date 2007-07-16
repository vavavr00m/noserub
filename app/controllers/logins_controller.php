<?php
/* SVN FILE: $Id:$ */
 
class LoginsController extends AppController {
    var $uses = array('Login');
    var $helpers = array('form');
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function login() {
        if(!empty($this->data)) {
            $login = $this->Login->check($this->data);
            if($login) {
                $this->Session->write('Login.id',       $login['Login']['id']);
                $this->Session->write('Login.username', $login['Login']['username']);
                $this->redirect('/noserub/' . urlencode(strtolower($login['Login']['username'])) . '/');
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
    function register() {
        if(!empty($this->data)) {
            if($this->Login->register($this->data)) {
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
        
        $this->set('verify_ok', $this->Login->verify($username, $hash));
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