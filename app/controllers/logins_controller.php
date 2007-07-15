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
                $this->redirect('/login/');
                exit;
            }
        }
    }
}