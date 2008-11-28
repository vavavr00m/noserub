<?php

class UrlUtil {
	
	public static function startsWithHttpOrHttps($url) {
    	if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
    		return true;
    	}
    	
    	return false;
    }
}