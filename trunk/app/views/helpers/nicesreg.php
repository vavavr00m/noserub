<?php
/**
 * Helper to format SReg data.
 */
class NiceSRegHelper extends AppHelper {
	
	public function key($key) {
		if ($key == 'dob') {
			return 'Date of birth';
		}
		
		return ucfirst($key);
	}
	
	public function value($key, $value) {
		$supportedFields = array('gender', 'email', 'fullname');
		$unsupportedFields = array('nickname', 'dob', 'postcode', 'country', 'language', 'timezone');
		$result = '(invalid)';
		
		if (in_array($key, $supportedFields)) {
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