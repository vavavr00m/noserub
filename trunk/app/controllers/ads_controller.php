<?php
/* SVN FILE: $Id:$ */
 
class AdsController extends AppController {
    public $uses = array('Ad');
    
    public function index() {
        if(!Context::isAdmin()) {
            $this->redirect('/admins/');
        }
    }
    
    public function add() {
 	    if(Context::isAdmin()) {
            if($this->RequestHandler->isPost()) {
                $this->data['Ad']['network_id'] = Context::networkId();
                $saveable = array(
                    'name', 'width', 'height', 'network_id',
                    'content', 'allow_php', 'created'
                );
                $this->Ad->create();
                if($this->Ad->save($this->data, true, $saveable)) {
                    $this->flashMessage('success', __('Ad created', true));
                } else {
                    $this->storeFormErrors('Ad', $this->data, $this->Ad->validationErrors);
                }
                $this->redirect('/admins/ads/');
            }
 	    }
 	}
 	
 	public function edit() {
 	    if(Context::isAdmin()) {
            if($this->RequestHandler->isPost()) {
                // test, that this ad belongs to the current network
                $this->Ad->id = $this->data['Ad']['id'];
                if($this->Ad->field('network_id') != Context::networkId()) {
                    $this->flashMessage('alert', __('The Ad does not belong to this network', true));
                    $this->redirect('/admins/ads/');
                }
                $saveable = array(
                    'name', 'width', 'height',
                    'content', 'allow_php'
                );
                if($this->Ad->save($this->data, true, $saveable)) {
                    $this->flashMessage('success', __('Ad saved', true));
                } else {
                    $this->storeFormErrors('Ad', $this->data, $this->Ad->validationErrors);
                }
                $this->redirect('/admins/ads/');
            }
 	    }
 	}
 	
 	public function assign() {
 	    if(Context::isAdmin()) {
            if($this->RequestHandler->isPost()) {
                $this->Ad->saveAssignment($this->data['Assignment']);
                $this->flashMessage('success', __('Assignment saved', true));
            }
        }
        $this->redirect('/admins/ads/');
 	}
}