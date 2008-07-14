<?php
class FeedsController extends AppController {
    public $uses = array('Feed');
    
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
        $this->Feed->Account->contain(array('Identity', 'Feed'));
        $data = $this->Feed->Account->find('all');

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
     * Refreshes 250 feed caches, ordered by last update.
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
            
            $this->Feed->contain(array('Account', 'Identity'));
            // TODO replace findAll with find('all')
            $data = $this->Feed->findAll(array('Feed.updated < "' . $last_refresh . '"'), null, 'Feed.updated ASC', 1);
            foreach($data as $item) {
                # set the updated right now, so a parallel running task
                # would not get it, while we are fetching the feed
                $this->Feed->id = $item['Feed']['id'];
                $this->Feed->saveField('updated', date('Y-m-d H:i:s'));
                
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