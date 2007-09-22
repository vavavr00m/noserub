<?php

class ClusterComponent extends Object {

    /**
     * Clusters arrays of arrays by the index 'datetime'.
     * Array needs to be sorted by datetime desc.
     * Clusters will by YYY-MM-DD DESC
     *
     * @param  array $data
     * @return array $clustered_data
     * @access 
     */
    public function create($data) {
        $clustered_data = array();
        foreach($data as $key => $value) {
            $day = date('Y-m-d', strtotime($value['datetime']));
            if(!isset($clustered_data[$day])) {
                $clustered_data[$day] = array();
            }
            $clustered_data[$day][] = $value; 
        }
    
        return $clustered_data;
    }
}

?>