<?php

class GeocoderComponent extends Object {

    /**
     * returns an array with latitude and longitude
     * for the given address or false, when no result
     * was found - or no API key is given. 
     *
     * @param  string $address
     * @return array with keys 'longitude', 'latitude'
     * @access 
     */
    public function get($address) {
        if(!defined('NOSERUB_GOOGLE_MAPS_KEY') || NOSERUB_GOOGLE_MAPS_KEY === false) {
            return false;
        }
        
        $url = 'http://maps.google.com/maps/geo?output=csv&q='. urlencode($address) . '&key=' . NOSERUB_GOOGLE_MAPS_KEY;
        
        $response = file_get_contents($url);
        
        if($response === false) {
            return false;
        }
        
        $result = split(',', $response);
        if($result[0] != 200) {
            return false;
        } 
        
        return array('latitude'  => $result[2],
                     'longitude' => $result[3]);
    } 
}

?>