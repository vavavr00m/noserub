<?php
class FeedsController extends AppController {
    var $uses = array('Feed');
    
    /**
     * Go through all accounts and create
     * initial feed caches. This should
     * be called from time to time.
     *
     * @param  
     * @return 
     * @access 
     */
    public function shell_create() {
        $this->Feed->Account->recursive = 1;
        $this->Feed->Account->expects('Account.Account', 'Account.Feed', 'Feed.Feed');
        $data = $this->Feed->Account->findAll();

        $created = array();
        foreach($data as $item) {
            if(!$item['Feed']['id'] && $item['Account']['feed_url']) {
                $feed_data = $this->Feed->Account->Service->feed2array($item['Account']['service_id'], $item['Account']['feed_url']);
                $this->Feed->store($item['Account']['id'], $feed_data);
                $created[] = $item['Account']['feed_url'];
            }
        }
        
        $this->set('data', $created);
    } 
    
    /**
     * Refreshes 250 feed caches, ordered by priority. Priority
     * is increased for every access to a feed.
     * Priority is set to 0 before each refresh to avoid endless
     * loops, when there is a problem with a specific feed.
     *
     * @param  
     * @return 
     */
    public function shell_refresh() {
        $this->Feed->recursive = 1;
        $this->Feed->expects('Feed.Feed', 'Feed.Account', 'Account.Account');
        $data = $this->Feed->findAll(null, null, 'Feed.priority DESC', 250);
        
        $refreshed = array();
        foreach($data as $item) {
            $feed_data = $this->Feed->Account->Service->feed2array($item['Account']['service_id'], $item['Account']['feed_url']);
            $this->Feed->store($item['Account']['id'], $feed_data);
            $refreshed[] = $item['Account']['feed_url'];
        }
        
        $this->set('data', $refreshed);
    }
    
}