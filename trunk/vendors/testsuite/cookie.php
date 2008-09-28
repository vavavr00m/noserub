<?php
/**
 * Mock cookie component.
 *
 * Copyright (c) 2007, Daniel Hofstetter (http://cakebaker.42dh.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version			$Revision$
 * @modifiedby		$LastChangedBy: dhofstet $
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

	class CookieComponent extends Object {
		var $cookieData = array();
		
		function &getInstance() {
			static $instance = array();
			
			if (!isset($instance[0]) || !$instance[0]) {
				$instance[0] =& new CookieComponent();
			}
	
			return $instance[0];
		}
		
		function write($key, $value = null, $encrypt = true, $expires = null) {
			$instance =& CookieComponent::getInstance();
			$instance->cookieData[$key] = $value;
		}
		
		function read($key = null) {
			$instance =& CookieComponent::getInstance();
			
			if ($key == null) {
				return $instance->cookieData;
			} else {
				return $instance->cookieData[$key];
			}
		}
		
		function del($key) {
			$instance =& CookieComponent::getInstance();
			
			if (array_key_exists($key, $instance->cookieData)) {
				unset($instance->cookieData[$key]);
			}
		}
		
		function destroy() {
			$instance =& CookieComponent::getInstance();
			$instance->cookieData = array();
		}
		
		function type($type = 'cipher') {
			// empty
		}
	}
?>