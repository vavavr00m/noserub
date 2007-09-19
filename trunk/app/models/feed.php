<?php
/* SVN FILE: $Id:$ */
 
class Feed extends AppModel {
    var $belongsTo = array('Account');
    
    /**
     * Store $data for $account_id to feed cache
     *
     * @param  int $account_id
     * @param  array $data
     * @param int $priority with which the entry should be created
     * @return 
     */
    public function store($account_id, $cache_data, $priority = 0) {
        if(!$cache_data) {
            return;
        }
        
        # check, if there already is a feed for this account in the feed
        $this->recursive = 0;
        $this->expects('Feed');
        $feed = $this->findByAccountId($account_id);
        if($feed) {
            $this->id = $feed['Feed']['id'];
        } else {
            $this->create();
        }
        
        $saveable = array('account_id', 'priority', 'content', 'created', 'modified');
        $data = array('account_id' => $account_id,
                      'priority'   => 0,
                      'content'    => serialize($cache_data));
        $this->save($data);
    }

    /**
     * accesses the cache and returns the data. Also increases
     * the priority for that feed.
     *
     * @param  int $account_id
     * @param  int $num_items
     * @param  time $max_age
     * @return 
     * @access 
     */
    function access($account_id, $num_items = 5, $items_max_age = '-14 days') {
        $this->recursive = 0;
        $this->expects('Feed');
        $feed = $this->findByAccountId($account_id);
        
        if(!$feed) {
            # we don't have a cache for that, so we create an empty entry
            # with a high priority, so it gets refreshed, soon.
            $this->store($account_id, array(), 100000);
            $cache_data = false;
        } else {
            $this->id = $feed['Feed']['id'];
            $this->saveField('priority', $feed['Feed']['priority'] + 1);
        
            $cache_data = unserialize($feed['Feed']['content']);
        }
        
        if(!$cache_data) {
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
            } else {
                $return_data[] = $item;
            }
        }
        return $return_data;
    }
}