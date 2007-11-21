<?php

class ClusterComponent extends Object {

    /**
     * Clusters arrays of arrays by the index 'datetime'.
     * Array needs to be sorted by datetime desc.
     * Clusters will by YYY-MM-DD DESC
     *
     * @param  array $data
     * @param  bool $with_future_dates 
     * @return array $clustered_data
     * @access 
     */
    public function create($data, $with_future_dates = false) {
        $today = date('Y-m-d');
        $clustered_data = array();
        foreach($data as $key => $value) {
            $day = date('Y-m-d', strtotime($value['datetime']));
            if($with_future_dates === false && $day > $today) {
                continue;
            }
            if(!isset($clustered_data[$day])) {
                $clustered_data[$day] = array();
            }
            $clustered_data[$day][] = $value; 
        }
    
        return $clustered_data;
    }
}

?>