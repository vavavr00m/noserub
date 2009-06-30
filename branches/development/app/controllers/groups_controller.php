<?php
class GroupsController extends AppController {
    public $uses = array('Group');
    
    public function index() {
    }
    
    /**
     * This is the page /:username/groups
     */
    public function profile() {
        Context::setPage('profile.groups');
    }
    
    public function add() {
        $this->grantAccess('user');
        if($this->RequestHandler->isPost()) {
            $this->ensureSecurityToken();
            $this->data['Group']['network_id'] = Context::networkId();
            $this->Group->create();
            $saveable = array(
                'network_id', 'name', 'description', 'slug',
                'modified', 'created'
            );
            if($this->Group->save($this->data, true, $saveable)) {
                $identity_id = Context::loggedInIdentityId();
                $this->Group->addSubscriber($identity_id);
                $this->Group->addAdmin($identity_id);
                $this->Group->Entry->addGroup($identity_id, $this->Group->id);
                
                $this->redirect('/groups/view/' . $this->Group->field('slug') . '/');
            }
        }
    }
    
    public function view($slug) {
        $group = $this->Group->find('first', array(
            'contain' => false,
            'conditions' => array(
                'network_id' => Context::networkId(),
                'slug' => strtolower($slug)
            ),
        ));
        if(!$group) {
            $this->redirect('/groups/');
        }
        
        Context::write('group', array(
            'id' => $group['Group']['id'],
            'slug' => $slug
        ));
        
        $this->Group->id = $group['Group']['id'];
        Context::write(
            'is_subscribed',
            $this->Group->isSubscribed(Context::loggedInIdentityId()
        ));
        
        $this->set('group', $group);
    }
    
    public function subscribe($slug) {
        $this->ensureSecurityToken();
        
        $group = $this->Group->find('first', array(
            'contain' => false,
            'conditions' => array(
                'Group.slug' => $slug
            ),
            'fields' => 'Group.id'
        ));
        
        if(!$group) {
            $this->flashMessage('alert', __('Could not subscribe to this group', true));
        } else {
            $this->Group->id = $group['Group']['id'];
            if($this->Group->addSubscriber(Context::loggedInIdentityId())) {
                $this->flashMessage('success', __('You are now subscribed to this group', true));
            } else {
                $this->flashMessage('alert', __('Could not subscribe to this group', true));
            }
        }
    
        $this->redirect($this->referer());
    }
    
    public function unsubscribe($slug) {
        $this->ensureSecurityToken();
        
        $group = $this->Group->find('first', array(
            'contain' => false,
            'conditions' => array(
                'Group.slug' => $slug
            ),
            'fields' => 'Group.id'
        ));
        
        if(!$group) {
            $this->flashMessage('alert', __('Could not unsubscribe to this group', true));
        } else {
            $this->Group->id = $group['Group']['id'];
            if($this->Group->removeSubscriber(Context::loggedInIdentityId())) {
                $this->flashMessage('success', __('You are no longer subscribed to this group', true));
            } else {
                $this->flashMessage('alert', __('Could not unsubscribe from this group', true));
            }
        }
    
        $this->redirect($this->referer());
    }
}