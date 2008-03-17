<?php
/* SVN FILE: $Id:$ */
 
class Location extends AppModel {
    var $belongsTo = array('Identity');                                                   

    public function setTo($identity_id, $location_id) {
        # check, that this location belongs to that identity
        $this->recursive = 0;
        $this->expects('Location');
        $location = $this->findById($location_id);
        if($location['Location']['identity_id'] != $identity_id) {
            return false;
        }
        
        $this->Identity->id = $identity_id;
        $this->Identity->saveField('last_location_id', $location_id);
        
        $this->Identity->Activity->setLocation($identity_id, $location);

        return true;
    }
    
    public function export($identity_id) {
        $this->recursive = 0;
        $this->expects('Location');
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
            $this->recursive = 0;
            $this->expects('Location');
            $conditions = array(
                'Location.identity_id' => $identity_id,
                'Location.name' => $item['name']
            );
            if($this->findCount($conditions) == 0) {
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
