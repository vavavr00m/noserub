<?php
/* SVN FILE: $Id:$ */
 
class Group extends AppModel {
    public $belongsTo = array('Network');
    
    public $hasMany = array('Entry');
    
    public $hasAndBelongsToMany = array(
        'GroupSubscriber' => array(
            'className'  => 'Identity',
            'joinTable'  => 'group_subscriptions',
            'foreignKey' => 'group_id',
            'associationForeignKey' => 'identity_id'
        ),
        'GroupMaintainer' => array(
            'className' => 'Identity',
            'joinTable' => 'group_admins',
            'foreignKey' => 'group_id',
            'associationForeignKey' => 'identity_id' 
        )
    );
    
    public $actsAs = array(
        'Sluggable' => array('label' => 'name')
    );
    
    public $validate = array(
        'name' => array(
            'rule' => 'notEmpty',
            'message' => '' # needs to be set in constructor due to gettext
        )
    );
    
    public function __construct() {
        parent::__construct();
        $this->validate['name']['message'] = __('You need to specify a group name', true);
    }
    
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
            'limit' => 5,
            'order' => 'entry_count DESC'
        ));
    }
    
    /**
     * I just couldn't get to get it work with the CakePHP routines...
     */
    public function addSubscriber($identity_id) {
        if(!$this->exists()) {
            return false;
        }
        $this->GroupSubscriber->id = $identity_id;
        if(!$this->GroupSubscriber->exists()) {
            return false;
        }
        
        $this->query('INSERT INTO ' . $this->tablePrefix . 'group_subscriptions (group_id, identity_id) VALUES (' . $this->id . ',' . $identity_id . ')');
    
        return true;
    }
    
    public function removeSubscriber($identity_id) {
        if(!$this->exists()) {
            return false;
        }
        $this->GroupSubscriber->id = $identity_id;
        if(!$this->GroupSubscriber->exists()) {
            return false;
        }
        
        if($this->isAdmin($identity_id)) {
            $this->removeAdmin($identity_id);
        }
        
        $data = $this->query('DELETE FROM ' . $this->tablePrefix . 'group_subscriptions WHERE group_id=' . intval($this->id) . ' AND identity_id=' . intval($identity_id) . ' LIMIT 1');
    
        return true;
    }
    
    /**
     * I just couldn't get to get it work with the CakePHP routines...
     */
    public function addAdmin($identity_id) {
        if(!$this->exists()) {
            return false;
        }
        $this->GroupMaintainer->id = $identity_id;
        if(!$this->GroupMaintainer->exists()) {
            return false;
        }
        
        $this->query('INSERT INTO ' . $this->tablePrefix . 'group_admins (group_id, identity_id) VALUES (' . $this->id . ',' . $identity_id . ')');
    
        return true;
    }
    
    public function removeAdmin($identity_id) {
        if(!$this->exists()) {
            return false;
        }
        $this->GroupMaintainer->id = $identity_id;
        if(!$this->GroupMaintainer->exists()) {
            return false;
        }
        
        $data = $this->query('DELETE FROM ' . $this->tablePrefix . 'group_admins WHERE group_id=' . intval($this->id) . ' AND identity_id=' . intval($identity_id) . ' LIMIT 1');
    
        return true;
    }
    
    /**
     * Returns wether this identity is subscribed to that group
     *
     * @param int $identity_id
     *
     * @return bool
     */
    public function isSubscribed($identity_id) {
        if(!$this->exists()) {
            return false;
        }
        
        // cant' get to get it work with the CakePHP routines...
        $data = $this->query('SELECT id FROM ' . $this->tablePrefix . 'group_subscriptions WHERE group_id=' . intval($this->id) . ' AND identity_id=' . intval($identity_id));
        
        if(!$data) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Returns wether this identity is admin for that group
     *
     * @param int $identity_id
     *
     * @return bool
     */
    public function isAdmin($identity_id) {
        if(!$this->exists()) {
            return false;
        }
        
        // cant' get to get it work with the CakePHP routines...
        $data = $this->query('SELECT id FROM ' . $this->tablePrefix . 'group_admins WHERE group_id=' . intval($this->id) . ' AND identity_id=' . intval($identity_id));
       
        if(!$data) {
            return false;
        } else {
            return true;
        }
    }
    
    public function saveInContext($data) {
        Context::write('group', array(
            'id' => $data['Group']['id'],
            'slug' => $data['Group']['slug'],
            'name' => $data['Group']['name']
        ));
    }
}
