<?php
/**
 * Helper to format SReg data.
 */
class NiceSRegHelper extends AppHelper {
	var $helpers = array('Form');
	var $supportedFields = array('gender', 'email', 'fullname');
	
	// TODO maybe a better name is needed?
	public function checkboxForSupportedFields($key) {
		if (in_array($key, $this->supportedFields)) {
			return $this->Form->checkBox('OpenidSite.'.$key, array('checked' => true));
		}
		
		return '';
	}
	
	public function key($key) {
		if ($key == 'dob') {
			return 'Date of birth';
		}
		
		return ucfirst($key);
	}
	
	public function value($key, $value) {
		$unsupportedFields = array('nickname', 'dob', 'postcode', 'country', 'language', 'timezone');
		$result = '(invalid)';
		
		if (in_array($key, $this->supportedFields)) {
			if ($key == 'gender') {
				if ($value == 'M') {
					$result = 'male';
				} elseif ($value == 'F') {
					$result = 'female';
				}
			} else {
				$result = $value;
			}
		} elseif (in_array($key, $unsupportedFields)) {
			$result = '(not supported by NoseRub)'; 
		}
		
		return $result;
	}
}
?>