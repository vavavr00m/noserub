<?php

class UrlComponent extends Object {

    /**
     * Makes sure, that an url is http and not https
     *
     * @param  string $url
     * @return string
     * @access 
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
}

?>