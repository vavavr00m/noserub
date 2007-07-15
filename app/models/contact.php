<?php
/* SVN FILE: $Id:$ */
 
class Contact extends AppModel {
    var $hasMany = array('FromIdentity' => array('className'  => 'Identity',
                                                 'foreignKey' => 'from_identity_id'),
                         'ToIdentity'   => array('className'  => 'Identity',
                                                 'foreignKey' => 'to_identity_id'));
}