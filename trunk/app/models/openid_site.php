<?php

class OpenidSite extends AppModel {
	var $belongsTo = array('Identity');
	
	public function updateAllAllowedStates($openidSites, $identityId) {
		$iterator = new FilterOpenidSites($openidSites);

		$this->updateAllowedStates($iterator, 1, $identityId);
		$this->updateAllowedStates($iterator, 0, $identityId);		
	}
	
	private function updateAllowedStates($iterator, $allowed, $identityId) {
		$keys = array();
		$iterator->setFilter($allowed);
		
		foreach ($iterator as $key => $value) {
			$keys[] = $key;
		}

		if (!empty($keys)) {
			$this->updateAll(array('allowed' => $allowed), array('OpenidSite.identity_id' => $identityId, 'OpenidSite.id' => $keys));
		}
	}
}

class FilterOpenidSites extends FilterIterator {
	private $filter = 1;
	
	public function __construct($array) {
		parent::__construct(new ArrayIterator($array));
	}
	
	public function accept() {
		return ($this->current() == $this->filter);
    }
    
    public function setFilter($filter) {
    	$this->filter = $filter;
    }
}
?>