<?php

class UrlComponent extends Object {

    /**
     * Makes sure, that an url is http and not https
     */
    public function http($url) {
        if($url == '' || $url === null) {
            return $url;
        }
        
        $url = str_replace('https://', 'http://', $url);

        if(strpos($url, 'http://') === false) {
            $url = FULL_BASE_URL . Router::url($url);
            $url = str_replace('https://', 'http://', $url);
        }
        
        return $url;
    }
    
    public function startsWithHttpOrHttps($url) {
    	if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
    		return true;
    	}
    	
    	return false;
    }
}