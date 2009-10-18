<?php
/* SVN FILE: $Id:$ */
 
class Group extends AppModel {
    public $belongsTo = array('Network');
    
    public $hasMany = array(
        'Entry' => array(
            'foreignKey' => 'foreign_key',
            'conditions' => array(
                'Entry.model' => 'group'
            )
        )
    );
    
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
        'Sluggable' => array(
            'label' => 'name', 
            'translation' => 'utf-8'
    ));
    
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
     * Get overview of groups with new
     * activities.
     */
    public function getOverview($options) {
        $limit = $this->getValue($options, 'limit', 10);
        $identity_id = $this->getValue($options, 'identity_id');
        if($identity_id) {
            # this is displayed in a context of an identity, so we only
            # get those groups where the user is subscribed to.
            $this->GroupSubscriber->id = $identity_id;
            $subscribed_groups = $this->GroupSubscriber->getSubscribedGroups();
            $subscribed_groups_id = Set::extract($subscribed_groups, '{n}.id');
            $conditions = array('Group.id' => $subscribed_groups_id);
        } else {
            $conditions = false;
        }
        return $this->find('all', array(
            'contain' => false,
            'conditions' => $conditions,
            'limit' => $limit,
            'order' => 'last_activity DESC'
        ));
    }
    
    public function getNew() {
        return $this->find('all', array(
            'contain' => false,
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
        return $this->find('all', array(
            'contain' => false,
            'limit' => 5,
            'order' => 'entry_count DESC'
        ));
    }
    
    /**
     * Returns $limit members of this group.
     *
     * @param int $limit
     *
     * @return array
     */
    public function getMembers($limit = 9) {
        if(!Context::groupId()) {
            return false;
        }
        $data = $this->find('first', array(
            'contain' => array(
                'GroupSubscriber' => array(
                    'limit' => 9,
                    'order' => 'last_login DESC'
                )
            ),
            'conditions' => array(
                'Group.id' => Context::groupId()
            )
        ));
        
        return $data['GroupSubscriber'];
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
    
        $this->updateSubscriberCount();
        
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
        
        $this->query('DELETE FROM ' . $this->tablePrefix . 'group_subscriptions WHERE group_id=' . intval($this->id) . ' AND identity_id=' . intval($identity_id) . ' LIMIT 1');
        
        $this->updateSubscriberCount();
        
        return true;
    }
    
    public function updateSubscriberCount() {
        if(!$this->exists()) {
            return false;
        }
        
        $data = $this->query('SELECT COUNT(*) FROM ' . $this->tablePrefix . 'group_subscriptions WHERE group_id=' . intval($this->id));
        if(isset($data[0][0]['COUNT(*)'])) {
            $this->saveField('subscriber_count', $data[0][0]['COUNT(*)']);
        }
    }
    
    public function updateLastActivity() {
        if(!$this->exists()) {
            return false;
        }
        $this->saveField('last_activity', date('Y-m-d H:i:s'));
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
            'name' => $data['Group']['name'],
            'last_activity' => $data['Group']['last_activity']
        ));
        
        $this->id = $data['Group']['id'];
        Context::write(
            'is_subscribed',
            $this->isSubscribed(Context::loggedInIdentityId()
        ));
    }
}
