<?php
/* SVN FILE: $Id:$ */
 
class Group extends AppModel {
    public $belongsTo = array('Network');
    
    // public $hasAndBelongsToMany = array(
    //             'GroupSubscriber' => array(
    //                 'className'  => 'Identity',
    //                 'joinTable'  => 'group_subscriptions',
    //                 'foreignKey' => 'group_id',
    //                 'associationForeignKey' => 'identity_id'
    //             ),
    //             'GroupAdmin' => array(
    //                 'className' => 'Identity',
    //                 'joinTable' => 'group_admins',
    //                 'foreignKey' => 'group_id',
    //                 'associationForeignKey' => 'identity_id' 
    //             )
    //     );
}