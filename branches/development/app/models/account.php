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
    function update($identity_id, $data) {
        # remove old account data
        $this->deleteByIdentityId($identity_id);
        
        # add the new data
        foreach($data as $account_info) {
            $account = array('Account' => $account_info);
            $account['Account']['identity_id'] = $identity_id;
            $saveable = array('identity_id', 'service_id', 'service_type_id', 'username', 
                              'account_url', 'feed_url', 'created', 'modified');
            $this->create();
            $this->save($account, true, $saveable);
        }
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
            $account['key'] = md5($account['id']);
            unset($account['id']);
            unset($account['identity_id']);
            unset($account['created']);
            unset($account['modified']);
            $accounts[] = $account;
        }
        return $accounts;
    }
}