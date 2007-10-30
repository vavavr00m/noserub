<?php
/**
 * Mock session component.
 *
 * Copyright (c) 2007, Daniel Hofstetter (http://cakebaker.42dh.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

	class SessionComponent extends Object {
		var $session = array();
		var $flash = array();
		
		function &getInstance() {
			static $instance = array();
			
			if (!isset($instance[0]) || !$instance[0]) {
				$instance[0] =& new SessionComponent();
			}
	
			return $instance[0];
		}
		
		function write($name, $value = null) {
			$instance =& SessionComponent::getInstance();
			$instance->session[$name] = $value;
		}
		
		function read($name = null) {
			$instance =& SessionComponent::getInstance();
			if ($name == null) {
				return $instance->session;
			} else {
				return $instance->session[$name];
			}
		}
		
		function del($name) {
			$instance =& SessionComponent::getInstance();
			
			if (array_key_exists($name, $instance->session)) {
				unset($instance->session[$name]);
			}
		}
		
		function delete($name) {
			$this->del($name);
		}
		
		function check($name) {
			$instance =& SessionComponent::getInstance();
			return (array_key_exists($name, $instance->session));
		}
		
		function error() {
			return false;
		}
		
		function flash($key = 'flash') {
			$this->del($key);
		}
		
		function renew() {}
		
		function valid() {
			return true;
		}
		
		function destroy() {
			$instance =& SessionComponent::getInstance();
			$instance->session = array();
		}
		
		function setFlash($flashMessage, $layout = 'default', $params = array(), $key = 'flash') {
			$instance =& SessionComponent::getInstance();
			$instance->flash[$key] = $flashMessage;
		}
	}
?>