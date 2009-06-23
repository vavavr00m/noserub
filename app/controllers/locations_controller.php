<?php
class LocationsController extends AppController {
    public $uses = array('Location');
    public $components = array('url', 'geocoder');
    
    public function settings() {
        $this->grantAccess('self');
        
        Context::setPage('settings.location');
    }
        
    public function add() {
        $this->grantAccess('self');
        
        if($this->RequestHandler->isPost()) {
            $this->ensureSecurityToken();
            
            if($this->data['Location']['name']) {
                if($this->saveData($this->data)) {
                    $this->flashMessage('success', __('Location added.', true));
                } else {
                    $this->flashMessage('error', __('Location could not be created.', true));
                }
            } else {
                $this->Location->invalidate('name', __('You need to specify a name.', true));
                $this->storeFormErrors('Location', $this->data, $this->Location->validationErrors);
            }
        }
        
        $this->redirect($this->referer());
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
            'identity_id', 'name', 'modified', 'created'
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