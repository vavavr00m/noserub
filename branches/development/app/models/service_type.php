<?php
/* SVN FILE: $Id:$ */
 
class ServiceType extends AppModel {
    public $useTable = false;
    
    private $allowedFilters = array(
        'photo'        => 'Photos',
	    'video'        => 'Videos',
	    'audio'        => 'Audio',
	    'link'         => 'Links',
	    'text'         => 'Texts',
	    'micropublish' => 'Micropublish',
	    'event'        => 'Events',
	    'document'     => 'Documents',
	    'location'     => 'Locations',
	    'noserub'      => 'NoseRub'
	);
	                            
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
	public function getList(array $tokens) {
	    $service_types = Configure::read('service_types');
	    $ids = array();
	    foreach($tokens as $token) {
	        foreach($service_types as $id => $service_type) {
	            if($service_type['token'] == $token) {
	                $ids[] = $id;
	            }
	        }
	    }
	    
	    return $ids;
	}
}