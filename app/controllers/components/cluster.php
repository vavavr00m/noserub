<?php

class ClusterComponent extends Object {

    /**
     * Clusters arrays of arrays by the index 'published_on'.
     * Array needs to be sorted by datetime desc.
     * Clusters will by YYY-MM-DD DESC
     *
     * @param  array $data
     * @param  bool $with_future_dates 
     * @return array $clustered_data
     */
    public function create($data, $with_future_dates = false) {
        $today = date('Y-m-d');
        $clustered_data = array();
        foreach($data as $key => $value) {
            $day = date('Y-m-d', strtotime($value['Entry']['published_on']));
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
    
    /**
     * removes duplicates from the original array,
     * *not* the cluster!
     *
     * @param array $data
     *
     * @return array
     */
    public function removeDuplicates($data) {
        $cleaned = array();
        $lookup_table = array();
        foreach($data as $idx => $item) {
            $key = $item['Entry']['identity_id'] . '.' . md5(strip_tags($item['Entry']['title']));
            
            if(isset($cleaned[$key])) {
                # we have this already
                if($item['Entry']['account_id'] == 0 && 
                   $cleaned[$key]['Entry']['account_id'] == 0) {
                    # if both are NoseRub, keep both!
                    $cleaned[$key . '.' . $idx] = $item;
                } else if($cleaned[$key]['Entry']['account_id'] != 0) {
                    # the existing (= newer) one is not NoseRub, 
                    # so replace it with current (= older) one
                    $cleaned[$key] = $item;
                }
                # else: always keep the newest
            } else {
                $cleaned[$key] = $item;
            }
        }
        
        usort($cleaned, 'sort_items');
        return $cleaned;
    }
}