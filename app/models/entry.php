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
     * 
     * @return array added entries
     */
    public function updateByAccountId($account_id) {
        $this->Account->id = $account_id;
        $identity_id     = $this->Account->field('identity_id');
        $service_type_id = $this->Account->field('service_type_id');
        
        $account_data = $this->Account->getData();
        if(!$account_data) {
            return array();
        }
        
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
        $entries = array();
        foreach($account_data as $item) {
            if($item['datetime'] >= $date_newest_item) {
                $entry = $this->update($identity_id, $account_id, $service_type_id, $item);
                if($entry) {
                    $entries[] = $entry; 
               }
            }
        }
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
            $entry = array(
                'identity_id'     => $identity_id,
                'account_id'      => $account_id,
                'service_type_id' => $service_type_id,
                'published_on'    => $item['datetime'],
                'title'           => $item['title'] ? $item['title'] : '',
                'url'             => $item['url'],
                'content'         => $item['content'],
                'restricted'      => 0
            );
            $saveable = array_keys($entry);
            $this->save($entry, $saveable, true);
            $entry['id'] = $this->id;
        }
        
        return $entry;
    }
}