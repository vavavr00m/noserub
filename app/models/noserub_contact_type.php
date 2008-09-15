<?php

class NoserubContactType extends AppModel {
	public $hasAndBelongsToMany = array('Contact');
	
	/**
	 * return ids of noserub contact types for the tags in
	 * the string. also removes thos tags from the new tags.
	 *
	 * @param string $tags space separated tags
	 * @return array of noserub contact type ids and cleaned up tags
	 */
	public function extract($new_tags) {
	    $tags = explode(' ', $new_tags);
	    $ids = array();
	    $remaining_tags = array();
	    foreach($tags as $tag) {
	        if($tag) {
	            $this->contain();
	            $data = $this->findByName($tag, array('id', 'name'));
                if($data) {
                    $ids[$data['NoserubContactType']['id']] = $data['NoserubContactType']['name'];
                } else {
                    $remaining_tags[] = $tag;
                }
            }
	    }

	    return array('noserub_contact_type_ids'  => $ids,
	                 'tags'                      => $remaining_tags);
	}

    /**
     * merges the array of noserub contact types (id => {0,1}) with
     * the tags in the string $new_tags
     */
	public function merge($noserub_contact_types, $new_tag_ids) {
	    foreach($noserub_contact_types as $id => $marked) {
	        if(isset($new_tag_ids[$id])) {
	            $noserub_contact_types[$id] = 1;
	        }
	    }
	    
	    return $noserub_contact_types;
	}
	
	public function getIDsForContact($contact_id) {
	    $this->ContactsNoserubContactType->contain();
		$noserub_contact_types    = $this->ContactsNoserubContactType->findAllByContactId($contact_id);
    	return(Set::extract($noserub_contact_types, '{n}.ContactsNoserubContactType.noserub_contact_type_id'));
	}
	
	public function getNoserubContactTypeIDsToAdd($currentlySelectedNoserubContactTypeIDs, $newlySelectedNoserubContactTypeIDs) {
		return $this->getElementsOnlyAvailableInFirstArray($newlySelectedNoserubContactTypeIDs, $currentlySelectedNoserubContactTypeIDs);
	}
	
	public function getNoserubContactTypeIDsToRemove($currentlySelectedNoserubContactTypeIDs, $newlySelectedNoserubContactTypeIDs) {
		return $this->getElementsOnlyAvailableInFirstArray($currentlySelectedNoserubContactTypeIDs, $newlySelectedNoserubContactTypeIDs);
	}
	
	/**
	 * @param array $data Array in the form: array('NoserubContactType' => array(1 => 1, 2 => 0, 3 => 1));
	 * @return array with IDs of the selected NoserubContactTypes
	 */
	public function getSelectedNoserubContactTypeIDs($data) {
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