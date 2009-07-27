<?php
/* SVN FILE: $Id:$ */
 
class Location extends AppModel {
    public $belongsTo = array('Identity');                                                   
    public $hasMany = array(
        'Entry' => array(
            'foreignKey' => 'foreign_key',
            'conditions' => array(
                'Entry.model' => 'location'
            )
        ),
        'Event'
    );
    
    public $actsAs = array(
        'Sluggable' => array(
            'label' => 'name', 
            'translation' => 'utf-8'
    ));
    
    public function getTypes() {
        return array(
            0 => __('Unknown', true),
            1 => __('Art & Entertainment', true),
            2 => __('Education', true),
            3 => __('Eating & Drinking', true),
            4 => __('Events', true),
            5 => __('Health & Beauty', true),
            6 => __('Hotel & Holidays', true),
            7 => __('Nightlife', true),
            8 => __('Services', true),
            9 => __('Shopping', true),
            10 => __('Sports', true),
            11 => __('Transportation', true),
            12 => __('Other', true)
        );
    }
    
    /**
     * Returns the last $limit locations this user created
     *
     * @param int $identity_id
     * @param int $limit
     *
     * @return array
     */
    public function getNew($identity_id, $limit = 5, $type = 'all') {
        return $this->find($type, array(
            'contain' => false,
            'conditions' => array(
                'identity_id' => $identity_id
            ),
            'limit' => $limit,
            'order' => 'created DESC'
        ));
    }
    
    /**
     * get all locations for the logged in identity
     *
     * @param int $identity_id
     *
     * @return array
     */
    public function get($identity_id) {
        return $this->find('all', array(
            'contain' => false,
            'conditions' => array(
                'Location.identity_id' => $identity_id
            ),
            'order' => 'Location.name'
        ));
    }
    
    public function setTo($identity_id, $location_id) {
        # check, that this location belongs to that identity
        $this->contain();
        $location = $this->findById($location_id);
        if($location['Location']['identity_id'] != $identity_id) {
            return false;
        }
        
        $this->Identity->id = $identity_id;
        $frontpage_updates = $this->Identity->field('frontpage_updates');
        
        $this->Identity->Entry->setLocation($identity_id, $location, $frontpage_updates == 0);

        return true;
    }
    
    public function export($identity_id) {
        $this->contain();
        $data = $this->findAllByIdentityId($identity_id);
        $locations = array();
        foreach($data as $item) {
            $location = $item['Location'];
            unset($location['id']);
            unset($location['identity_id']);
            unset($location['created']);
            unset($location['modified']);
            $locations[] = $location;
        }
        return $locations;
    }
    
    public function import($identity_id, $data) {
        foreach($data as $item) {
            # first check, if we already have a location by this name
            $this->contain();
            $conditions = array(
                'Location.identity_id' => $identity_id,
                'Location.name' => $item['name']
            );
            if (!$this->hasAny($conditions)) {
                $this->create();
                $saveable = array('identity_id', 'name', 'address', 'latitude', 'longitude');
                $item['identity_id'] = $identity_id;
                if(!$this->save($item, true, $saveable)) {
                    return false;
                }
            }
        }
        return true;
    }
    
    public function updateLastActivity() {
        if(!$this->exists()) {
            return false;
        }
        $this->saveField('last_activity', date('Y-m-d H:i:s'));
    }
    
    public function saveInContext($data) {
        Context::write('location', array(
            'id' => $data['Location']['id'],
            'slug' => $data['Location']['slug'],
            'name' => $data['Location']['name'],
            'latitude' => $data['Location']['latitude'],
            'longitude' => $data['Location']['longitude'],
            'last_activity' => $data['Location']['last_activity']
        ));
    }
}
