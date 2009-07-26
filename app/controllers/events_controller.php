<?php
class EventsController extends AppController {
    public $uses = array('Event');
    
    public function index() {
    }
    
    /**
     * This is the page /:username/events
     */
    public function profile() {
        Context::setPage('profile.events');
    }
    
    public function add() {
        $this->grantAccess('self');
        
        if($this->RequestHandler->isPost()) {
            $this->ensureSecurityToken();
            $this->data['Event']['from_datetime'] = $this->Event->deconstruct('from_datetime', $this->data['Event']['from_datetime']);
            $this->data['Event']['to_datetime'] = $this->Event->deconstruct('to_datetime', $this->data['Event']['to_datetime']);
            $identity_id = Context::loggedInIdentityId();
            $this->data['Event']['identity_id'] = $identity_id;
            if($this->Event->save($this->data)) {
                $this->Event->Entry->addEvent($identity_id, $this->Event->id);
                $this->flashMessage('success', __('Event added.', true));
                $this->redirect('/events/view/' . $this->Event->id);
            } else {
                $this->storeFormErrors('Event', $this->data, $this->Event->validationErrors);
            }
            
        }
        
        Context::setPage('profile.events');
    }
    
    public function view($id) {
        $event = $this->Event->find('first', array(
            'contain' => array('Location'),
            'conditions' => array(
                'Event.id' => $id
            ),
        ));
        if(!$event) {
            $this->redirect('/events/');
        }
        
        $this->Event->saveInContext($event);
    }
}