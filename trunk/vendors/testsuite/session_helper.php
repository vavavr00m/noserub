<?php
/**
 * Dummy implementation of the session helper. 
 * 
 * Doesn't implement the public functions of the CakeSession class, which is the parent class 
 * of the SessionHelper.
 */

	class SessionHelper {
		var $helpers = null;
		
		function activate($base = null) {
			// empty
		}
		
		function read($name = null) {
			return 'value';
		}
		
		function check($name) {
			return true;
		}
		
		function error() {
			return false;
		}
		
		function flash($key = 'flash') {
			return true;
		}
		
		function valid() {
			return true;
		}
	}
?>