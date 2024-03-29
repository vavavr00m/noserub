<?php

class ClusterComponent extends Object {

    /**
     * Clusters arrays of arrays by the index 'published_on'.
     * Array needs to be sorted by datetime desc.
     * Clusters will by YYY-MM-DD DESC
     *
     * @param  array $data
     * @return array $clustered_data
     */
    public function create($data, $with_future_dates = false) {
        $now = date('Y-m-d H:i:s');
        $clustered_data = array();
        foreach($data as $key => $value) {
            $day = date('Y-m-d', strtotime($value['Entry']['published_on']));
            if($with_future_dates === false && 
               $value['Entry']['published_on'] > $now) {
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
     * @param bool $look_at_comments if enabled, entries with comments
     *        are not removed, although they have the same content.
     *        TODO: check, wether the comments are identical and
     *              therefore the entries could be removed.
     * @return array
     */
    public function removeDuplicates($data, $look_at_comments = true) {
        $cleaned = array();
        foreach($data as $idx => $item) {
            $title = $item['Entry']['title'] ? $item['Entry']['title'] : $item['Entry']['id'];
            $key = $item['Entry']['identity_id'] . '.' . md5(strip_tags($title));
            
            if(isset($cleaned[$key])) {
                $both_with_comments = (
                    ($cleaned[$key]['Comment'] || $cleaned[$key]['FavoritedBy']) &&
                    ($item['Comment'] || $item['FavoritedBy'])
                );

                # we already have this
                if($look_at_comments && $both_with_comments) {
                    # if both have comments/favorites, keep both!
                    $cleaned[$key . '.' . $idx] = $item;
                } else if(!$cleaned[$key]['Comment'] && !$cleaned[$key]['FavoritedBy']) {
                    # if existing one has no comments/favorites, 
                    # replace it with current one
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