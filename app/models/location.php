<?php
/* SVN FILE: $Id:$ */
 
class Location extends AppModel {
    var $belongsTo = array('Identity');                                                   

    public function set($identity_id, $location_id) {
        $data = array('last_location_id' => $location_id);
        $this->Identity->id = $identity_id;
        $this->Identity->save($data);
        
        # we need to get the complete location for the activity
        $this->id = $location_id;
        $this->recursive = 0;
        $this->expects('Location');
        $location = $this->findById($location_id);
        $this->Identity->Activity->setLocation($identity_id, $location);
    }
}