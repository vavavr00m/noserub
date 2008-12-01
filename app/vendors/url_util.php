<?php

class UrlUtil {
	
	public static function addHttpIfNoProtocolSpecified($url) {
		if (!UrlUtil::startsWithHttpOrHttps($url) && trim($url) != '') {
			$url = 'http://' . $url;
		}
		
		return $url;
	}
	
	public static function startsWithHttpOrHttps($url) {
    	if (stripos($url, 'http://') === 0 || stripos($url, 'https://') === 0) {
    		return true;
    	}
    	
    	return false;
    }
}