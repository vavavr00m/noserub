<?php
/* SVN FILE: $Id:$ */
 
class Contact extends AppModel {
    var $belongsTo = array('Identity',
                           'WithIdentity' => array('className' => 'Identity',
                                                   'foreinKey' => 'with_identity_id'));
                                                   
    var $validate = array(
            'username' => array('content'  => array('rule' => array('custom', '/^[\da-zA-Z-\.\_]+$/')),
                                'required' => VALID_NOT_EMPTY),
            'email'    => array('mail'     => VALID_EMAIL,
                                'required' => VALID_NOT_EMPTY),
            'passwd'   => array('rule' => array('minLength', 6)),
            'passwd2'  => array('rule' => 'validatePasswd2')
        );
}