<?php
/* SVN FILE: $Id:$ */
 
class Feed extends AppModel {
    public $belongsTo = array('Account');
    
    /**
     * Store $data for $account_id to feed cache
     *
     * @param  int $account_id
     * @param  array $data
     * @return 
     */
    public function store($account_id, $cache_data) {
        if(!$cache_data) {
            return;
        }
        
        # find the newest item in cache_data
        $date_newest_item = '2007-01-01 00:00:01';
        foreach($cache_data as $item) {
            if($item['datetime'] > $date_newest_item) {
                $date_newest_item = $item['datetime'];
            }
        }
        
        # check, if there already is a feed for this account in the feed
        $this->contain();
        $feed = $this->findByAccountId($account_id);
        if($feed) {
            $this->id = $feed['Feed']['id'];
        } else {
            $this->create();
        }
        
        $saveable = array('account_id', 'content', 'date_newest_item', 'created', 'modified');
        $data = array('account_id'       => $account_id,
                      'content'          => @serialize($cache_data),
                      'date_newest_item' => $date_newest_item);
        $this->save($data);
        
        return $date_newest_item;
    }

    /**
     * accesses the cache and returns the data.
     *
     * @param  int $account_id
     * @param  int $num_items
     * @param  time $max_age
     * @return 
     * @access 
     */
    public function access($account_id, $num_items = 5, $items_max_age = '-14 days') {
        $this->contain();
        $feed = $this->findByAccountId($account_id);
        
        if(!$feed) {
            # we don't have a cache for that, so we create an empty entry
            # which will get refreshed soon.
            $this->store($account_id, array());
            $cache_data = false;
        } else {
            $cache_data = @unserialize($feed['Feed']['content']);
        }
        
        if(!$cache_data || !is_array($cache_data)) {
            $cache_data = array();
        }
        
        # now apply the rules about max num and age
        $max_age = $items_max_age ? date('Y-m-d H:i:s', strtotime($items_max_age)) : null;
        $return_data = array();
        foreach($cache_data as $item) {
            if(count($return_data) == $num_items) {
                break;
            }
            
            if($max_age && $item['datetime'] > $max_age) {
                $return_data[] = $item;
            } else if(!$max_age) {
                $return_data[] = $item;
            }
        }
        return $return_data;
    }
    
    /**
     * Updates the $service_id for a given account to a new
     * service_id
     *
     * @param int $account_id
     * @param int $new_service_id
     */
    public function updateServiceType($account_id, $new_service_id) {
        $this->contain();
        $feed = $this->findByAccountId($account_id);
        $data = @unserialize($feed['Feed']['content']);
        if($data) {
            # get intro for new service
            $this->Account->ServiceType->recursive = 0;
            $this->Account->ServiceType->expects('ServiceType');
            $this->Account->ServiceType->id = $new_service_id;
            $new_intro = $this->Account->ServiceType->field('intro');

            foreach($data as $idx => $item) {
                $data[$idx]['intro'] = $new_intro;
            }
            
            $this->id = $feed['Feed']['id'];
            $this->saveField('content', @serialize($data));
        }
    }
}