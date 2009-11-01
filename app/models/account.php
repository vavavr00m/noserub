<?php
/* SVN FILE: $Id:$ */
 
class Account extends AppModel {
    public $belongsTo = array('Identity');
    public $hasMany = array(
        'Entry' => array(
            'foreignKey' => 'foreign_key',
            'conditions' => array(
                'Entry.model' => 'account'
            )
        )
    );
    
    public $validate = array(
            'username' => array('content'  => array('rule' => array('custom', '/^[\da-zA-Z-\.@\_ ]+$/')),
                                'required' => VALID_NOT_EMPTY));
         
    
    /**
     * get all accounts for given identity_id
     *
     * @param int $identity_id
     * @param string $type 'all' => all accounts
     *                     'web' => all web accounts
     *                     'communication' => all communication accounts
     *
     * @return array
     */
    public function get($identity_id, $type = 'all') {
        $conditions = array(
            'Account.identity_id' => $identity_id
        );
        switch($type) {
            case 'web':
                $conditions['Account.is_contact'] = 0;
                break;
                
            case 'communication':
                $conditions['Account.is_contact'] = 1;
                break;
        }
        
        return $this->find('all', array(
            'contain' => false,
            'conditions' => $conditions
        ));
    }
    
    /**
     * get's data from RSS (for example) of this
     * account.
     *
     * @return array
     */
    public function getData() {
        $this->contain();
        $account = $this->read();
        $account = $account['Account'];
        
        App::import('Model', 'Service');
        $this->Service = new Service();
        
        return $this->Service->feed2array(
            $account['username'],
            $account['service'],
            $account['service_type'],
            $account['feed_url'],
            50 // number of items
        );
    }
    
    /**
     * @TODO make sure everything associated with this account is deleted
     */
    public function deleteWithAssociated() {
        $this->Entry->deleteByAccountId($this->id);
        $this->delete();
    }
    
    /**
     * 
     */
    public function update($identity_id, $data) {
        # get all accounts for this identity, so we afterwards can decide
        # wether an account needs to be removed.
        $this->contain();
        $accounts = $this->find(
            'all',
            array(
                'conditions' => array(
                    'identity_id' => $identity_id
                )
            )
        );
        
        # add the new data
        foreach($data as $item) {
            # check, if we already have it. we need to do this, even
            # when not replacing. it could be, that an account is
            # more than once in the array.
            
            $conditions = array(
                'Account.identity_id' => $identity_id,
            );
            
            if($item['feed_url']) {
                # only add feed URL, when there actually is one.
                # else, all the contact things like AIM, Jabber would fail.
                $conditions['Account.feed_url'] = $item['feed_url'];
            } else {
                $conditions['Account.account_url'] = $item['account_url'];
            }

            $this->cacheQueries = false;
            $this->contain();
            $account = $this->find('first', array('conditions' => $conditions));
            if(!$account) {
                $this->log('new account: ' . $item['account_url'], LOG_DEBUG);
                $item['identity_id'] = $identity_id;
                $saveable = array(
                    'identity_id', 'service', 'service_type', 'title',
                    'username', 'account_url', 'feed_url', 'created', 'modified'
                );
                $this->create();
                $this->save($item, true, $saveable);
            } else {
                $this->log('existing account: ' . $item['account_url'], LOG_DEBUG);
                $saveable = array(
                    'identity_id', 'service', 'service_type', 'title',
                    'username', 'account_url', 'feed_url', 'modified'
                );
                $item['identity_id'] = $identity_id;
                $this->id = $account['Account']['id'];
                $this->save($item, true, $saveable);
            
                # delete this account from the $accounts array
                foreach($accounts as $key => $value) {
                    if($value['Account']['id'] == $account['Account']['id']) {
                        unset($accounts[$key]);
                        break;
                    }
                }
            }
        }
        
        # delete all accounts that were not found
        foreach($accounts as $item) {
            $this->log('delete account: ' . $item['Account']['account_url'], LOG_DEBUG);
            $this->delete($item['Account']['id'], false);
            $this->Entry->deleteAll(
                array(
                    'foreign_key' => $item['Account']['id'],
                    'model' => 'account'
                ), 
                false
            );
        }
        
        return true;
    }
        
    /**
     * Deletes all accounts for given identity_id
     *
     * @param  $identity_id for which all accounts should be removed
     * @return 
     * @access 
     */
    public function deleteByIdentityId($identity_id) {
        $data = $this->find('all', array(
            'contain' => false,
            'conditions' => array(
                'identity_id' => $identity_id
        )));
        foreach($data as $item) {
            # delete account and feed cache
            $account_id = $item['Account']['id'];
            $this->delete($account_id, false);
            $this->Entry->deleteAll(
                array(
                    'foreign_key' => $item['Account']['id'],
                    'model' => 'account'
                ), 
                false
            );
        }
    }
    
    public function export($identity_id) {
        $this->contain();
        $data = $this->findAllByIdentityId($identity_id);
        $accounts = array();
        foreach($data as $item) {
            $account = $item['Account'];
            unset($account['id']);
            unset($account['identity_id']);
            unset($account['created']);
            unset($account['modified']);
            $accounts[] = $account;
        }
        return $accounts;
    }
    
    public function import($identity_id, $data) {
        return $this->update($identity_id, $data);
    }
}