<?php
/* SVN FILE: $Id:$ */
 
class Identity extends AppModel {
    var $hasMany = array('Contact', 'Account');
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
        if($this->data['Identity']['passwd'] !==$value) {
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
        $this->expects('Identity');
        if($this->findCount(array('Identity.username = "' . $value . '"')) > 0) {
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
        $this->expects('Identity');
        return $this->find(array('Identity.hash' => '',
                                 'Identity.username = "'. $data['Identity']['username'] .'"', 
                                 'Identity.password' => md5($data['Identity']['password'])));
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
        $data['Identity']['password'] = md5($data['Identity']['passwd']);
        $data['Identity']['hash'] = md5(time().$data['Identity']['username']);
        $saveable = array('username', 'password', 'email', 'hash', 'created', 'modified');
        if(!$this->save($data, true, $saveable)) {
            return false;
        }
        
        # send out verification mail
        $msg  = 'Welcome to NoseRub!' . "\n\n";
        $msg .= 'please click here to verify you email address:' ."\n";
        $msg .= FULL_BASE_URL . '/verify/' . $data['Identity']['username'] . '/' . $data['Identity']['hash'] . '/' . "\n\n";
        $msg .= 'If you do not click on this link, the account will automatically be deleted after 14 days.' . "\n\n";
        $msg .= 'Thanks!';
        
        if(!mail($data['Identity']['email'], 'Your NoseRub registration', $msg, 'From: info@noserub.com')) {
            LogError('verify mail could not be sent: '.$data['Identity']['email']);
        }
        
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
        $this->expects = array('Identity');
        $identity = $this->find(array('Identity.username' => $username, 'Identity.hash' => $hash));
        if($username && $hash && $identity) {
            # update the identity
            $this->id = $identity['Identity']['id'];
            return $this->saveField('hash', '');
        } else {
            return false;
        }
    }
}