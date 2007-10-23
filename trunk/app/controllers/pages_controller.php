<?php
class PagesController extends AppController {
    var $uses = array('Identity');
    var $components = array('cluster');
    
    function display() {
        $this->socialStream();
    }
    
    function socialStream() {
        $filter = isset($this->params['filter']) ? $this->params['filter']   : '';
        
        # sanitize filter
        switch($filter) {
            case 'photo':
            case 'video':
            case 'audio':
            case 'link':
            case 'text':
            case 'event':
            case 'micropublish':
            case 'document':
            case 'location':
                $filter = $filter; 
                break;
            
            default: 
                $filter = false;
        }
        
        $this->Identity->recursive = 2;
        $this->Identity->expects('Identity.Identity', 'Identity.Account', 
                                 'Account.Account', 'Account.Service', 'Account.ServiceType',
                                 'Service.Service',
                                 'ServiceType.ServiceType');
        $data = $this->Identity->findAll(array('frontpage_updates' => 1,
                                               'is_local'  => 1,
                                               'hash'      => '',
                                               'NOT username LIKE "%@%"'),
                                         null, null, 10);

        # extract the identities
        
        $items      = array();
        $identities = array();
        foreach($data as $identity) {
            
            # extract the identities
            if(count($identities) < 9) {
                $identities[] = $identity['Identity'];
            }
            
            # get all items for those accounts
            if(is_array($identity['Account'])) {
                foreach($identity['Account'] as $account) {
                    if(!$filter || $account['ServiceType']['token'] == $filter) {
                        if(defined('NOSERUB_USE_FEED_CACHE') && NOSERUB_USE_FEED_CACHE) {
                            $new_items = $this->Identity->Account->Feed->access($account['id'], 5, false);
                        } else {
                            $new_items = $this->Identity->Account->Service->feed2array($username, $account['service_id'], $account['service_type_id'], $account['feed_url'], 5, false);
                        }
                        if($new_items) {
                            $items = array_merge($items, $new_items);
                        }
                    }
                }
            }
        }
        usort($items, 'sort_items');
        $items = $this->cluster->create($items);

        $this->set('data', $data);
        $this->set('identities', $identities);
        $this->set('items', $items);
        $this->set('filter', $filter);
        
        $this->set('headline', 'All public social activities');
        $this->render('social_stream');
    }
}