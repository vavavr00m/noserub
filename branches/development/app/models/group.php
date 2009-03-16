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
    
    
    /**
     * get overview of all groups
     */
    public function getOverview() {
        $this->contain();
        return $this->find('all', array(
            'limit' => 10
        ));
    }
    
    public function getNew() {
        $this->contain();
        return $this->find('all', array(
            'limit' => 5,
            'order' => 'created DESC'
        ));
    }
    
    /**
     * fake! TODO: look into how to get the number
     * of subscribers or number of entries into the
     * "popularity" measurement.
     */
    public function getPopular() {
        $this->contain();
        return $this->find('all', array(
            'limit' => 5
        ));
    }
}
