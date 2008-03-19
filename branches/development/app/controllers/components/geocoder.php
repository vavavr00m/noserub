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
        
        $response = @file_get_contents($url);
        
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
    
    /**
     * Returns distance in km
     *
     * @param float $latitude1
     * @param float $longitude1
     * @param float $latitude2
     * @param float $longitude2
     * @return float distance in km 
     */
    public function distance($latitude1, $longitude1, $latitude2, $longitude2) {        
        $theta = $longitude1 - $longitude2;
        if($theta == 0) {
            return 0;
        }
        $distance = sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)) +  cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)); 
        $distance = acos($distance);
        if(is_nan($distance)) {
            # acos for this value is not defined
            # mostly this is because of two points
            # that are too close together. during
            # processing, those values got rounded.
            # therefore, it is ok to return a distance
            # of 0 km.
            return 0;
        }
        $distance = rad2deg($distance); 
        $distance_mile = $distance * 60 * 1.1515;
        $distance_km   = $distance_mile * 1.609344;

        if(!is_float($distance_km)) {
            return 0;
        } else {
            return $distance_km;
        }
    }
}

?>