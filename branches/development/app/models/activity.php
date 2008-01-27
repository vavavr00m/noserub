<?php
/* SVN FILE: $Id:$ */
 
class Activity extends AppModel {
    var $belongsTo = array('Identity', 'ServiceType');
    
    public function getLatest($identity_id, $filter) {
        $items = array();
        
        if(in_array('location', $filter)) {
            $items = $this->getLocations($identity_id);
        }
        
        return $items;
    }
    
    public function getLocations($identity_id) {
        $items = array();
        
        $this->recursive = 1;
        $this->expects('Location', 'ServiceType', 'Identity');
        $data = $this->findAllByIdentityId($identity_id);
        
        foreach($data as $activity) {
            $item = array();
		    $item['datetime'] = $activity['Activity']['created'];
		    		
		    $item['title']    = $activity['Activity']['content'];
		    $item['url']      = '';
            $item['intro']    = $activity['ServiceType']['intro'];
            $item['type']     = $activity['ServiceType']['token'];
            $item['username'] = $activity['Identity']['username'];
            $item['content']  = $activity['Activity']['content'];
            
		    $items[] = $item;
		}
		
        return $items;
    }
    
    public function setLocation($identity_id, $location) {
        if($location && isset($location['Location']['name']) && $location['Location']['name'] != '') {
            $data = array('identity_id'     => $identity_id,
                          'service_type_id' => 9,
                          'content'         => $location['Location']['name'],
                          'created'         => date('Y-m-d H:i:s'));
                      
            $this->create();
            $this->save($data);
            
            # update user's last_activity
            # not so nice to update user model data here
            $this->Identity->updateLastActivity();
            return true;
        } else {
            return false;
        }
    }                                                   
}