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
        $Identity->contain();
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
        $Identity->Entry->id = $entry_id;
        $Identity->id = $Identity->Entry->field('identity_id');    
        $Identity->contain();
        $favorite_identity = $Identity->read();    
        if($favorite_identity['Identity']['notify_favorite']) {
            $Identity->id = $identity_id;
            $Identity->contain();
            $data = $Identity->read();
            
            $this->send(array(
                'template' => 'entry/notify_favorite',
                'to'       => $favorite_identity['Identity']['email'],
                'subject'  => sprintf(__('%s: Someone marked your entry a favorite', true), Configure::read('NoseRub.app_name')),
                'data' => array(
                    'username'    => $data['Identity']['username'],
                    'entry_id'    => $entry_id,
                    'entry_title' => $Identity->Entry->field('title')
                )
            ));
        }
    }
    
    public function notifyComment($identity_id, $entry_id, $comment) {
        App::import('Model', 'Identity');
        $Identity = new Identity();
        
        # get the owner of this Entry and check, if he wants to be
        # notified. and make sure he did not comment on his own stuff.
        $Identity->Entry->id = $entry_id;
        $Identity->id = $Identity->Entry->field('identity_id');    
        $Identity->contain();
        $comment_identity = $Identity->read();
        if($comment_identity['Identity']['id'] != $identity_id &&
           $comment_identity['Identity']['notify_comment']) {
            $Identity->id = $identity_id;
            $Identity->contain();
            $data = $Identity->read();            

            $this->send(array(
                'template' => 'entry/notify_comment',
                'to'       => $comment_identity['Identity']['email'],
                'subject'  => sprintf(__('%s: Someone commented on your entry', true), Configure::read('NoseRub.app_name')),
                'data' => array(
                    'username'    => $data['Identity']['username'],
                    'entry_id'    => $entry_id,
                    'entry_title' => $Identity->Entry->field('title'),
                    'comment'     => $comment
                )
            ));
        }
    }
    
    public function notifyContact($identity_id, $contacted_identity_id) {
        App::import('Model', 'Identity');
        $Identity = new Identity();
        
        $Identity->id = $contacted_identity_id;
        $Identity->contain();
        $contacted_identity = $Identity->read();
        if($contacted_identity['Identity']['notify_contact']) {
            $Identity->id = $identity_id;
            $Identity->contain();
            $data = $Identity->read();
            
            $this->send(array(
                'template' => 'identity/notify_contact',
                'to'       => $contacted_identity['Identity']['email'],
                'subject'  => sprintf(__('%s: Someone added you as contact', true), Configure::read('NoseRub.app_name')),
                'data' => array(
                    'username' => $data['Identity']['username']
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