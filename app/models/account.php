<?php
/* SVN FILE: $Id:$ */
 
class Account extends AppModel {
    var $belongsTo = array('Identity', 'Service', 'ServiceType');
    
    var $hasOne = array('Feed');
    
    var $hasAndBelongsToMany = array('Syndication');
    
    var $validate = array(
            'username' => array('content'  => array('rule' => array('custom', '/^[\da-zA-Z-\.@\_ ]+$/')),
                                'required' => VALID_NOT_EMPTY));

    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function update($identity_id, $data, $replace = false) {
        if($replace) {
            # remove old account data
            $this->deleteByIdentityId($identity_id);
        }
    
        # add the new data
        foreach($data as $item) {
            # check, if we already have it. we need to do this, even
            # when not replacing. it could be, that an account is
            # more than once in the array.
            $urls = array('Account.account_url' => $item['account_url']);
            if($item['feed_url']) {
                # only add test for feed URL, when there actually is one.
                # else, all the contact things like AIM, Jabber would fail.
                $urls['Account.feed_url'] = $item['feed_url'];
            }
            $conditions = array(
                'Account.identity_id' => $identity_id,
                array('OR' => $urls)
            );
            $this->recursive = 0;
            $this->expects('Account');
            if($this->findCount($conditions) == 0) {
                $item['identity_id'] = $identity_id;
                $saveable = array(
                    'identity_id', 'service_id', 'service_type_id', 'title',
                    'username', 'account_url', 'feed_url', 'created', 'modified'
                );
                $this->create();
                $this->save($item, true, $saveable);
            }
        }
        
        return true;
    }
        
    function replace($identity_id, $data) {
        return $this->update($identity_id, $data, true);
    }
    
    /**
     * Deletes all accounts for given identity_id
     *
     * @param  $identity_id for which all accounts should be removed
     * @return 
     * @access 
     */
    function deleteByIdentityId($identity_id) {
        $this->recursive = 0;
        $this->expects('Account');
        $data = $this->findAllByIdentityId($identity_id);
        foreach($data as $item) {
            # delete account and feed cache
            $account_id = $item['Account']['id'];
            $this->delete($account_id, false);
            $this->execute('DELETE FROM ' . $this->tablePrefix . 'feeds WHERE account_id=' . $item['Account']['id']);
        }
    }
    
    public function export($identity_id) {
        $this->recursive = 0;
        $this->expects('Account');
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