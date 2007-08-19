<?php
/* SVN FILE: $Id:$ */
 
class Contact extends AppModel {
    var $belongsTo = array('Identity',
                           'WithIdentity' => array('className' => 'Identity',
                                                   'foreinKey' => 'with_identity_id'));
                                                   
    var $validate = array(
            'username' => array('content'  => array('rule' => array('custom', NOSERUB_VALID_USERNAME)),
                                'required' => VALID_NOT_EMPTY)
        );
}