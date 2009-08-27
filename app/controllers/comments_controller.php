<?php
class CommentsController extends AppController {
    public $uses = array('Comment');
    
    /**
     * adding a comment to an entry
     */
    public function add() {
        // @todo if entry belongs to a group, make sure
        // the user is subscribed to that group
        
        $identity_id = Context::loggedInIdentityId();
        $entry_id    = $this->data['Comment']['entry_id'];
        
        if($identity_id && $entry_id &&
           $this->RequestHandler->isPost()) {
               
            $this->ensureSecurityToken();
            
            if(!$this->data['Comment']['text']) {
                $this->flashMessage('alert', __('You forgot to write something.', true));
            } else {
                $this->flashMessage('success', __('Comment added.', true));

                $data = array(
                    'entry_id'     => $entry_id,
                    'identity_id'  => $identity_id,
                    'content'      => $this->data['Comment']['text'],
                    'published_on' => date('Y-m-d H:m:i')
                );
                $this->Comment->createForAll($data);
                $this->data = array();

                $this->Comment->Entry->addComment($identity_id, $entry_id);
            }
        }
        
        $this->redirect($this->referer());
    }
}