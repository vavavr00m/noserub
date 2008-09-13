<?php
/* SVN FILE: $Id:$ */
 
class Entry extends AppModel {
    public $belongsTo = array('Identity', 'Account', 'ServiceType');
    
    public $validate = array(
            'username' => array('content'  => array('rule' => array('custom', '/^[\da-zA-Z-\.@\_ ]+$/')),
                                'required' => VALID_NOT_EMPTY));

    
    /**
     * updates the entries
     *
     * @param int $account_id
     * @param bool $check_next_update
     * 
     * @return array added entries
     */
    public function updateByAccountId($account_id, $check_next_update = false) {
        $this->Account->id = $account_id;
        $this->Account->contain(array('Service.minutes_between_updates'));
        $account = $this->Account->read();

        if($check_next_update &&
           $account['Account']['next_update'] > date('Y-m-d H:i:s')) {
            # no need for an update yet
            return array();
        }
        
        $identity_id     = $account['Account']['identity_id'];
        $service_type_id = $account['Account']['service_type_id'];
        
        $account_data = $this->Account->getData();
        $entries = array();
        if($account_data) {
            # get date of newest item in db
            $this->contain();
            $conditions = array(
                'identity_id' => $identity_id,
                'account_id'  => $account_id
            );
            $fields = array('MAX(published_on)');
            $entry_data = $this->find(
                'all',
                array(
                    'conditions' => array(
                        'identity_id' => $identity_id,
                        'account_id'  => $account_id
                    ),
                    'fields' => array(
                        'MAX(published_on)'
                    )
                )
            ); 

            if(!$entry_data[0][0]['MAX(published_on)']) {
                $date_newest_item = '2000-01-01 00:00:00';
            } else {
                $date_newest_item = $entry_data[0][0]['MAX(published_on)'];
            }

            # get the new items
            foreach($account_data as $item) {
                if($item['datetime'] >= $date_newest_item) {
                    $entry = $this->update($identity_id, $account_id, $service_type_id, $item);
                    if($entry) {
                        $entries[] = $entry; 
                   }
                }
            }
        }

        # update account
        $minutes_between_updates = $account['Service']['minutes_between_updates'];
        if(!$minutes_between_updates) {
            # this account is not properly attached to a service
            $minutes_between_updates = 360; 
        }
        $next_update = date('Y-m-d H:i:s', strtotime('+' . $minutes_between_updates . ' minutes'));
        $this->Account->id = $account_id;
        $this->Account->saveField('next_update', $next_update);
        
        return $entries;
    }

    /**
     * updates/creates one single entry
     */
    public function update($identity_id, $account_id, $service_type_id, $item) {
        # search, if there is an entry with the same url
        $this->contain();
        $this->cacheQueries = false;
        $entry = $this->find(
            'first',
            array(
                'conditions' => array(
                    'identity_id' => $identity_id,
                    'account_id'  => $account_id,
                    'url'         => $item['url']
                )
            )
        );
        
        if($entry) {
            # check for update
            $entry = $entry['Entry'];
            if($entry['published_on'] < $item['datetime']) {
                # update
                $entry['published_on'] = $item['datetime'];
                $entry['title']        = $item['title'];
                $entry['content']      = $item['content'];
                $this->id = $entry['id'];
                $saveable = array('published_on', 'title', 'content');
                $this->save($entry, $saveable, true);
            } else {
                # needs no update
                $entry = false;
            }
        } else {
            # create
            $this->create();
            
            # find out, wether we need to restrict this entry.
            # right now, we only look for the general setting
            # in the identity.
            $this->Identity->id = $identity_id;
            $frontpage_updates = $this->Identity->field('frontpage_updates');
            
            $entry = array(
                'identity_id'     => $identity_id,
                'account_id'      => $account_id,
                'service_type_id' => $service_type_id,
                'published_on'    => $item['datetime'],
                'title'           => $item['title'] ? $item['title'] : '',
                'url'             => $item['url'],
                'content'         => $item['content'] ? $item['content'] : '',
                'restricted'      => !$frontpage_updates
            );
            $saveable = array_keys($entry);
            $this->save($entry, $saveable, true);
            $entry['id'] = $this->id;
        }
        
        return $entry;
    }
    
    /**
     */
    public function getForDisplay($account_id, $limit, $with_restricted = false) {
        if(!NOSERUB_MANUAL_FEEDS_UPDATE) {
            # update it before getting data
            $this->updateByAccountId($account_id, true);
        }
        
        $this->Identity->Entry->contain(
            array(
                'ServiceType.token',
                'ServiceType.intro',
                'Identity.firstname',
                'Identity.lastname',
                'Identity.username'
            )
        );
        $conditions = array(
            'account_id' => $account_id
        );
        if(!$with_restricted) {
            $conditions['restricted'] = 0;
        }
        $new_items = $this->Identity->Entry->find(
            'all',
            array(
                'conditions' => $conditions,
                'limit'      => $limit
            )
        );
    
        return $new_items;
    }
    
    /**
     */
    public function getMessage($entry) {
        $this->Identity->contain();
        $fields = array(
            'Identity.firstname',
            'Identity.lastname',
            'Identity.username'
        );
        $identity = $this->Identity->findById($entry['identity_id'], $fields);
        
        $this->ServiceType->contain();
        $fields = array(
            'ServiceType.token',
            'ServiceType.intro'
        );
        $service_type = $this->ServiceType->findById($entry['service_type_id'], $fields);
        
        $splitted = split('/', $identity['Identity']['username']);
        $splitted2 = split('@', $splitted[count($splitted)-1]);
        $username = $splitted2[0];
        $intro = str_replace('@user@', 'http://'.$identity['Identity']['username'], $service_type['ServiceType']['intro']);
        if($entry['url']) {
            $intro = str_replace('@item@', '»'.$entry['title'].' ('.$entry['url'].')«', $intro);
        } else {
            $intro = str_replace('@item@', '»'.$entry['title'].'«', $intro);
        }
        
        return $intro;
    }
}