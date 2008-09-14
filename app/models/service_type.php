<?php
/* SVN FILE: $Id:$ */
 
class ServiceType extends AppModel {
    public $belongsTo = array('Service');                                                   

    private $allowedFilters = array('photo'        => 'Photos',
	                            'video'        => 'Videos',
	                            'audio'        => 'Audio',
	                            'link'         => 'Link',
	                            'text'         => 'Text',
	                            'micropublish' => 'Micropublish',
	                            'event'        => 'Event',
	                            'document'     => 'Documents',
	                            'location'     => 'Locations');
	                            
	public function sanitizeFilter($filter) {
		$sanitizedFilter = false;
	    if(isset($this->allowedFilters[$filter])) {
	        $sanitizedFilter = $filter;
	    }
        
        return $sanitizedFilter;
	}
	
	/**
	 * Is used for creating the checkboxes in the
	 * Display-Settings.
	 */
	public function getFilters() {
	    return $this->allowedFilters;
	}
	
	public function getDefaultFilters() {
	    $default_filter = $this->allowedFilters;
	    unset($default_filter['audio']);
	    return array_keys($default_filter);
	}
	
	/**
	 * returns array of ids for an array of tokens
	 */
	public function getList($tokens) {
	    $ids = array();
	    foreach($tokens as $token) {
	        $this->contain();
	        $fields = array('id');
	        $data = $this->findByToken($token);
	        if($data) {
	        	$ids[] = $data['ServiceType']['id'];
	        }
	    }
	    
	    return $ids;
	}
}