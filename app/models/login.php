<?php
/* SVN FILE: $Id:$ */
 
class Login extends AppModel {
    var $hasMany = array('Identity');
    
    var $validate = array(
            'username' => array('content'  => array('rule' => array('custom', '/^[\da-zA-Z-\.\_]+$/')),
                                'unique'   => array('rule' => 'validateUniqueUsername'),
                                'required' => VALID_NOT_EMPTY),
            'passwd'  => array('rule' => array('minLength', 6)),
            'passwd2' => array('rule' => 'validatePasswd2')
        );
    
    function validatePasswd2($value, $params = array()) {
        if($this->data['Login']['passwd'] !==$value) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * validate, if the username is already taken
     *
     * @param  
     * @return 
     * @access 
     */
    function validateUniqueUsername($value, $params = array()) {
        $this->recursive = 0;
        $this->expects('Login');
        if($this->findCount(array('Login.username = "' . $value . '"')) > 0) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    public function check($data) {
        $this->recursive = 0;
        $this->expects('Login');
        return $this->find(array('Login.username = "'. $data['Login']['username'] .'"', 'Login.password' => md5($data['Login']['password'])));
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    public function register($data) {
        $this->create();
        $data['Login']['password'] = md5($data['Login']['passwd']);
        $saveable = array('username', 'password', 'created', 'modified');
        return $this->save($data, true, $saveable);
    }
}