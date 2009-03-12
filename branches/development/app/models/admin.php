<?php
/* SVN FILE: $Id:$ */
 
class Admin extends AppModel {
    # this may not be set, as system/update wouldn't work then!
    # public $belongsTo = array('Network');
    
    public $validate = array(
        'password' => array(
            'passwordMatch' => array(
                'rule' => array('validatePasswordMatch'),
                'message' => '' # must be set via beforeValidate due to using gettext
            ),
            'passwordLength' => array(
                'rule'    => array('validatePasswordLength'),
                'message' => '' # must be set via beforeValidate due to using gettext
            )
        )
    );
        
    public function beforeValidate($options = array()) {
        $this->validate['password']['passwordMatch']['message'] = __('The two new passwords do not match.', true);
        $this->validate['password']['passwordLength']['message'] = __('The new password must at least be 6 characters long.', true);
        return parent::beforeValidate($options);
    }
    
    public function validatePasswordMatch($data) {
        return $this->data['Admin']['new_password'] == $this->data['Admin']['new_password2'];
    }
    
    public function validatePasswordLength($data) {
        return strlen($this->data['Admin']['new_password']) >= 6;
    }
}