<?php
/* SVN FILE: $Id:$ */
 
class MessagesController extends AppController {
    public $uses = array('Message');
    
    public function index() {
        $this->redirect('/messages/inbox/');
    }
    
    public function inbox() {
        $this->grantAccess('self');
    }
    
    public function sent() {
        $this->grantAccess('self');
    }
    
    public function view($message_id) {
        $this->grantAccess('self');
        Context::write('message_id', $message_id);
    }
    
    public function reply($message_id) {
        $this->grantAccess('self');
        Context::write('message_id', $message_id);
    }
    
    public function add() {
        $this->grantAccess('self');
        
        if(isset($this->params['named']['to'])) {
            Context::write('message_to_identity_id', $this->params['named']['to']);
        }
        
        if($this->RequestHandler->isPost()) {
            $this->ensureSecurityToken();

            $identity_id = $this->Message->Identity->username2IdentityId($this->data['Message']['to_from']);
            if(!$identity_id) {
                $this->flashMessage('alert', __('Could not find recipient', true));
                $this->redirect($this->referer());
            }
            
            // create message for recipient
            $data = array(
                'identity_id' => $identity_id,
                'to_from' => Context::read('logged_in_identity.username'),
                'subject' => $this->data['Message']['subject'],
                'text' => $this->data['Message']['text'],
                'folder' => 'inbox',
            );

            $this->Message->create();
            if(!$this->Message->save($data)) {
                $this->flashMessage('alert', __('Could not send message', true));
                $this->redirect($this->referer());
            } 
            
            // create message for sender
            $data['identity_id'] = Context::loggedInIdentityId();
            $data['folder'] = 'sent';
            $data['read'] = 1;
            $data['to_from'] = $this->data['Message']['to_from'];
            $this->Message->create();
            $this->Message->save($data);
            $this->flashMessage('success', __('Message sent', true));

            $this->redirect('/messages/inbox/');
        }
    }
}