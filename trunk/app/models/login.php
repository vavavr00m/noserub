<?php
/* SVN FILE: $Id:$ */
 
class Login extends AppModel {
    var $hasMany = array('Identity');
    
    var $validate = array(
            'username' => array('content'  => array('rule' => array('custom', '/^[\da-zA-Z-\.\_]+$/')),
                                'unique'   => array('rule' => 'validateUniqueUsername'),
                                'required' => VALID_NOT_EMPTY),
            'email'    => array('mail'     => VALID_EMAIL,
                                'required' => VALID_NOT_EMPTY),
            'passwd'   => array('rule' => array('minLength', 6)),
            'passwd2'  => array('rule' => 'validatePasswd2')
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
        return $this->find(array('Login.hash' => '',
                                 'Login.username = "'. $data['Login']['username'] .'"', 
                                 'Login.password' => md5($data['Login']['password'])));
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
        $data['Login']['hash'] = md5(time().$data['Login']['username']);
        $saveable = array('username', 'password', 'email', 'hash', 'created', 'modified');
        if(!$this->save($data, true, $saveable)) {
            return false;
        }
        
        # send out verification mail
        $msg  = 'Welcome to NoseRub!' . "\n\n";
        $msg .= 'please click here to verify you email address:' ."\n";
        $msg .= FULL_BASE_URL . '/verify/' . $data['Login']['username'] . '/' . $data['Login']['hash'] . '/' . "\n\n";
        $msg .= 'If you do not click on this link, the account will automatically be deleted after 14 days.' . "\n\n";
        $msg .= 'Thanks!';
        
        mail($data['Login']['email'], 'Your NoseRub registration', $msg, 'From: info@noserub.com');
        
        return true;
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function verify($username, $hash) {
        # check, if there is a username with that hash
        $this->recursive = 0;
        $this->expects = array('Login');
        $login = $this->find(array('Login.username' => $username, 'Login.hash' => $hash));
        if($username && $hash && $login) {
            # update the login
            $this->id = $login['Login']['id'];
            return $this->saveField('hash', '');
        } else {
            return false;
        }
    }
}