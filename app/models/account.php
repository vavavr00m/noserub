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
}