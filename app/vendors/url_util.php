<?php

class UrlUtil {
	
	public static function addHttpIfNoProtocolSpecified($url) {
		if (!UrlUtil::startsWithHttpOrHttps($url) && trim($url) != '') {
			$url = 'http://' . $url;
		}
		
		return $url;
	}
	
	public static function removeHttpAndHttps($url) {
		if (UrlUtil::startsWithHttp($url)) {
			$url = substr($url, strlen('http://'));
		} elseif (UrlUtil::startsWithHttps($url)) {
			$url = substr($url, strlen('https://'));
		}
		
		return $url;
	}
	
	public static function startsWithHttp($url) {
		return (stripos($url, 'http://') === 0);
	}
	
	public static function startsWithHttpOrHttps($url) {
    	return (UrlUtil::startsWithHttp($url) || UrlUtil::startsWithHttps($url));
    }
    
    public static function startsWithHttps($url) {
    	return (stripos($url, 'https://') === 0);
    }
}