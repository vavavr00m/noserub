<?php
class LocationsController extends AppController {
    public $uses = array('Location');
    public $components = array('url', 'geocoder');
    
    public function index() {
    }
    
    /**
     * This is the page /:username/locations
     */
    public function profile() {
        Context::setPage('profile.locations');
    }
        
    public function add() {
        $this->grantAccess('self');
        
        if($this->RequestHandler->isPost()) {
            $this->ensureSecurityToken();
            
            if($this->data['Location']['name']) {
                if($this->saveData($this->data)) {
                    $this->Location->updateLastActivity();
                    $this->Location->Entry->addLocation(Context::loggedInIdentityId(), $this->Location->id);
                    $this->flashMessage('success', __('Location added.', true));
                    $this->redirect('/locations/view/' . $this->Location->id);
                } else {
                    $this->flashMessage('error', __('Location could not be created.', true));
                    $this->redirect($this->referer());
                }
            } else {
                $this->Location->invalidate('name', __('You need to specify a name.', true));
                $this->storeFormErrors('Location', $this->data, $this->Location->validationErrors);
            }
        }
        
        Context::setPage('profile.locations');
    }
    
    public function view($id) {
        $location = $this->Location->find('first', array(
            'contain' => false,
            'conditions' => array(
                'id' => $id
            ),
        ));
        if(!$location) {
            $this->redirect('/locations/');
        }
        
        $this->Location->saveInContext($location);
    }
    
    protected function saveData($data) {
        if($data['Location']['address']) {
            $geolocation = $this->geocoder->get($data['Location']['address']);
            if($geolocation !== false) {
                $data['Location']['latitude']  = $geolocation['latitude'];
                $data['Location']['longitude'] = $geolocation['longitude'];
            } else {
                $data['Location']['latitude']  = 0;
                $data['Location']['longitude'] = 0;
            }
        }
        $data['Location']['identity_id'] = Context::loggedInIdentityId();
        if(!@$data['Location']['id']) {
            $this->Location->create();
        }
        
        $saveable = array(
            'latitude', 'longitude', 'address', 
            'type', 'description', 'url',
            'identity_id', 'name', 'created'
        );
        
        return $this->Location->save($data, true, $saveable);
    }
    
    public function edit() {
        $this->grantAccess('self');
        
        if($this->RequestHandler->isPut()) {
            $this->ensureSecurityToken();
            
            if($this->data['Location']['name']) {
                if($this->saveData($this->data)) {
                    $this->flashMessage('success', __('Changes have been saved', true));
                } else {
                    $this->flashMessage('error', __('Changes could not be saved', true));
                }
            } else {
                $this->Location->invalidate('name', __('You need to specify a name.', true));
                $this->storeFormErrors('Location', $this->data, $this->Location->validationErrors);
            }
            
            $this->redirect('/settings/locations/');
        }
    }
    
    public function delete() {
        $this->grantAccess('self');
        
        if($this->RequestHandler->isPut()) {
            $this->ensureSecurityToken();
            
            $location = $this->Location->find(
                'first',
                array(
                    'conditions' => array(
                        'Location.id' => $this->data['Location']['id'],
                        'Location.identity_id' => Context::loggedInIdentityId()
                    )
                )
            );
            if($location) {
                $this->Location->id = $this->data['Location']['id'];
                $this->Location->delete();
                $this->flashMessage('success', __('Location has been deleted', true));
            }
        }
        
        $this->redirect('/settings/locations/');
    }
}