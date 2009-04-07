<?php
class GroupsController extends AppController {
    public $uses = array('Group');
    
    public function index() {
    
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
                $this->Group->addSubscriber(Context::loggedInIdentityId());
                $this->Group->addAdmin(Context::loggedInIdentityId());
                
                $this->redirect('/groups/view/' . $this->Group->field('slug') . '/');
            }
        }
    }
    
    public function view($slug) {
        $this->Group->contain();
        $group = $this->Group->find('first', array(
            'conditions' => array(
                'network_id' => Context::networkId(),
                'slug' => strtolower($slug)
            ),
            'fields' => 'Group.id'
        ));
        if(!$group) {
            $this->redirect('/groups/');
        }
    }
}