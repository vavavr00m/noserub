<?php
/* SVN FILE: $Id:$ */
 
class Location extends AppModel {
    public $belongsTo = array('Identity');                                                   

    public function setTo($identity_id, $location_id) {
        # check, that this location belongs to that identity
        $this->contain();
        $location = $this->findById($location_id);
        if($location['Location']['identity_id'] != $identity_id) {
            return false;
        }
        
        $this->Identity->id = $identity_id;
        $this->Identity->saveField('last_location_id', $location_id);
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
}
