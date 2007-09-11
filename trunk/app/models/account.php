<?php
/* SVN FILE: $Id:$ */
 
class Account extends AppModel {
    var $belongsTo = array('Identity', 'Service', 'ServiceType');
    
    var $validate = array(
            'username' => array('content'  => array('rule' => array('custom', '/^[\da-zA-Z-\.\_]+$/')),
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
        $this->execute('DELETE FROM accounts WHERE identity_id='.$identity_id);
        
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
        $identity_id = intval($identity_id);
        return $this->execute('DELETE FROM accounts WHERE identity_id='.$identity_id);
    }
    
    /**
     * sync that identity with data from username (url)
     *
     * @param  
     * @return 
     * @access 
     */
    function sync($identity_id, $username) {
        $this->log('sync('.$identity_id.', '.$username.')', LOG_DEBUG);
        # get the data from the remote server. try https:// and
        # http://
        $protocols = array('https://', 'http://');
        foreach($protocols as $protocol) {
            $data = $this->Identity->parseNoseRubPage($protocol . $username);
            if($data) {
                # we had success, so we don't need to try
                # the remaining protocol(s)
                continue;
            }
        }
        
        if(!$data) {
            # no data was found!
            return false;
        }
        
        # update all accounts for that identity
        $this->update($identity_id, $data);

        # update 'last_sync' field
        $this->Identity->id = $identity_id;
        $this->Identity->saveField('last_sync', date('Y-m-d H:i:s'));
        
        return true;
    }
}