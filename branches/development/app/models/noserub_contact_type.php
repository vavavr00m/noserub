<?php

class NoserubContactType extends AppModel {
	var $hasAndBelongsToMany = array('Contact');
	
	function getNoserubContactTypeIDsForContact($contactId) {
		$noserubContactTypes = $this->ContactsNoserubContactType->findAllByContactId($contactId);
    	$noserubContactTypeIDs = Set::extract($noserubContactTypes, '{n}.ContactsNoserubContactType.noserub_contact_type_id');
    	
    	return $noserubContactTypeIDs;
	}
	
	function getNoserubContactTypeIDsToAdd($currentlySelectedNoserubContactTypeIDs, $newlySelectedNoserubContactTypeIDs) {
		return $this->getElementsOnlyAvailableInFirstArray($newlySelectedNoserubContactTypeIDs, $currentlySelectedNoserubContactTypeIDs);
	}
	
	function getNoserubContactTypeIDsToRemove($currentlySelectedNoserubContactTypeIDs, $newlySelectedNoserubContactTypeIDs) {
		return $this->getElementsOnlyAvailableInFirstArray($currentlySelectedNoserubContactTypeIDs, $newlySelectedNoserubContactTypeIDs);
	}
	
	/**
	 * @param array $data Array in the form: array('NoserubContactType' => array(1 => 1, 2 => 0, 3 => 1));
	 * @return array with IDs of the selected NoserubContactTypes
	 */
	function getSelectedNoserubContactTypeIDs($data) {
    	if (empty($data) || !isset($data['NoserubContactType'])) {
    		return array();
    	}
    	
    	$iterator = new SelectedNoserubContactTypesFilter($data['NoserubContactType']);
    	$noserubContactTypeIDs = array();
    	
    	foreach ($iterator as $key => $value) {
    		$noserubContactTypeIDs[] = $key;
    	}
    	
    	return $noserubContactTypeIDs;
    }
    
    private function getElementsOnlyAvailableInFirstArray($firstArray, $secondArray) {
    	$differences = array();
		
		foreach ($firstArray as $value) {
			if (!in_array($value, $secondArray)) {
				$differences[] = $value;
			}
		}
		
		return $differences;
    }
}

class SelectedNoserubContactTypesFilter extends FilterIterator {
	private $selected = 1;
	
	public function __construct($array) {
		parent::__construct(new ArrayIterator($array));
	}
	
	public function accept() {
		return ($this->current() == $this->selected);
    }
}
?>