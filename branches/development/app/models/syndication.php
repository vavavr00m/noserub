<?php
/* SVN FILE: $Id:$ */
 
class Syndication extends AppModel {
    var $belongsTo = array('Identity');
    
    var $hasAndBelongsToMany = array('Account');
    
    public function export($identity_id) {
        $this->recursive = 1;
        $this->expects('Syndication', 'Account');
        $data = $this->findAllByIdentityId($identity_id);
        $syndications = array();
        foreach($data as $item) {
            $syndication = array('name' => $item['Syndication']['name']);
            $accounts = array();
            foreach($item['Account'] as $account) {
                $accounts[] = md5($account['id']);
            }
            $syndication['accounts'] = $accounts;
            $syndications[] = $syndication;
        }
        return $syndications;
    }
}