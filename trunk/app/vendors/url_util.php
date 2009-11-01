<?php

class UrlUtil {
	
	/**
	 * removes, http:// or https:// from an url,
	 * also www. and a trailing slash (/).
	 * also makes the url all lowercase
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public static function unify($url) {
	    $url = strtolower($url);
	    $url = rtrim($url, '/');
	    $url = UrlUtil::removeHttpWww($url);
	    
	    return $url;
	}
	
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
	
	/**
     * removes http://, https:// and www. from url
     */
     public static function removeHttpWww($url) {
    	$url = UrlUtil::removeHttpAndHttps($url);
        if(stripos($url, 'www.') === 0) {
            $url = substr($url, 4);
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