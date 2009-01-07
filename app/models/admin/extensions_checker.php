<?php

class ExtensionsChecker {
	
	public static function check() {
		$result = array();
    	
		if (!extension_loaded('spl')) {
			$result = am($result, array('SPL (Standard PHP Library)' => __('must be enabled', true)));
		}
		
        if (!extension_loaded('curl')) {
        	$result = am($result, array('curl' => __('needed for communicating with other servers', true)));
        }
        
    	if (!extension_loaded('gd')) {
        	$result = am($result, array('GD' => __('needed for image handling', true))); 
        }
    	
        if (!(function_exists('gmp_init') || function_exists('bcscale'))) {
        	$result = am($result, array(__('GMP or BCMath', true) => __('needed for OpenID functionality', true)));
        }
        
        return $result;
	}
}