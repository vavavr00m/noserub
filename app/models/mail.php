<?php
/* SVN FILE: $Id:$ */
 
/**
 * This model capsules all mail sending tasks from
 * NoseRub. 
 * 
 * We do this in order to send the mails from models, not
 * conrollers. Therefore, a requestAction is made for every
 * mail we send.
 */
class Mail extends AppModel {
    
    public $useTable = false;
    
    public function passwordRecovery($identity_id) {
        App::import('Model', 'Identity');
        $Identity = new Identity();
        
        $Identity->id = $identity_id;
        $data = $Identity->read();
        $username = $data['Identity']['username'];
        $email    = $data['Identity']['email'];
        
        $recovery_hash = md5(uniqid(rand(), true));
        $Identity->saveField('password_recovery_hash', $recovery_hash);
        
        $this->send(array(
            'template' => 'identity/password_recovery',
            'to'       => $email,
            'subject'  => sprintf(__('%s password recovery', true), Configure::read('NoseRub.app_name')),
            'data' => array(
                'recovery_hash' => $recovery_hash,
                'username'      => $username
            )
        ));
    }
    
    public function notifyFavorite($identity_id, $entry_id) {
        App::import('Model', 'Identity');
        $Identity = new Identity();
        
        # get the owner of this Entry and check, if he wants to be
        # notified
        $Identity->id = $identity_id;
        $data = $Identity->read();
        if($data['Identity']['notify_favorite']) {
            App::import('Model', 'Entry');
            $Entry = new Entry();
            $Entry->id = $entry_id;
            
            $this->send(array(
                'template' => 'entry/notify_favorite',
                'to'       => $data['Identity']['email'],
                'subject'  => sprintf(__('%s: Someone marked your entry a favorite', true), Configure::read('NoseRub.app_name')),
                'data' => array(
                    'username'    => $data['Identity']['username'],
                    'entry_id'    => $entry_id,
                    'entry_title' => $Entry->field('title')
                )
            ));
        }
    }
    
    public function registerIdentity($email, $hash) {
        $this->send(array(
            'template' => 'identity/register',
            'to'       => $email,
            'subject'  => sprintf(__('Your %s registration', true), Configure::read('NoseRub.app_name')),
            'data' => array(
                'hash' => $hash
            )
        ));
    }
    
    protected function send($data) {
        if(!isset($data['from'])) {
            $data['from'] = Configure::read('NoseRub.email_from');
        }
        $this->log(print_r($data, 1), LOG_DEBUG);
        $this->requestAction('/jobs/send_mail/', $data);
    }
}