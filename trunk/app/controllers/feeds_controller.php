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
        $this->Feed->Account->expects('Account.Account', 'Account.Identity', 'Identity.Identity', 'Account.Feed', 'Feed.Feed');
        $data = $this->Feed->Account->findAll();

        $created = array();
        foreach($data as $item) {
            if(!$item['Feed']['id'] && $item['Account']['feed_url']) {
                $feed_data = $this->Feed->Account->Service->feed2array($item['Identity']['username'], $item['Account']['service_id'], $item['Account']['service_type_id'], $item['Account']['feed_url'], 10, false);
                $date_newest_item = $this->Feed->store($item['Account']['id'], $feed_data);
                $this->Feed->Account->Identity->updateLastActivity($date_newest_item, $item['Identity']['id']);
                $created[] = $item['Account']['feed_url'];
            }
        }
        
        $this->set('data', $created);
    } 
    
    /**
     * Refreshes 250 feed caches, ordered by priority. Priority
     * is increased for every access to a feed.
     *
     * @param  
     * @return 
     */
    public function shell_refresh() {
        $refreshed = array();

        # I do this like this and not through a LIMIT of 250, because
        # then more than one task could run at once, without doing any harm
        for($i=0; $i<250; $i++) {
            # no two refresh's within 30 minutes
            $last_refresh = date('Y-m-d H:i:s', strtotime('-30 minutes'));
            
            $this->Feed->recursive = 2;
            $this->Feed->expects('Feed.Feed', 'Feed.Account', 'Account.Account', 'Account.Identity', 'Identity.Identity');
            $data = $this->Feed->findAll(array('Feed.modified < "' . $last_refresh . '"'), null, 'Feed.modified ASC, Feed.priority DESC', 1);

            foreach($data as $item) {
                # set the modified right now, so a parallel running task
                # would not get it, while we are fetching the feed
                $this->Feed->id = $item['Feed']['id'];
                $this->Feed->saveField('modified', date('Y-m-d H:i:s'));
                
                if($item['Account']['feed_url']) {
                    # get the actual feed. Maximum of 10 items, but without time restriction
                    $feed_data = $this->Feed->Account->Service->feed2array($item['Account']['Identity']['username'], $item['Account']['service_id'], $item['Account']['service_type_id'], $item['Account']['feed_url'], 10, false);
                
                    # save it to the cache
                    $date_newest_item = $this->Feed->store($item['Account']['id'], $feed_data);
                    $this->Feed->Account->Identity->updateLastActivity($date_newest_item, $item['Account']['Identity']['id']);
                    $refreshed[] = $item['Account']['feed_url'];
                }
            }
        }
        $this->set('data', $refreshed);
    }
    
}