<?php

class OpenidSitesController extends AppController {
	public $uses = array('OpenidSite');
	
	public function index() {
	    $this->checkSecure();
	    
	    $this->loadModel('Identity');
        $username = isset($this->params['username']) ? $this->params['username'] : '';
        $splitted = $this->Identity->splitUsername($username);
        $session_identity = $this->Session->read('Identity');
        if(!$session_identity) {
            # only a logged in user will see this
            $this->redirect('/');
        }
        
        # get identity that is displayed
        $this->Identity->contain();
        $identity = $this->Identity->findByUsername($splitted['username']);
        if(!$identity || 
           $identity['Identity']['id'] != $session_identity['id']) {
            # this identity is not here, or it is not the logged
            # in identity
            $this->redirect('/');
        }
        
		$this->set('headline', __('OpenID settings', true));
		
		if (!empty($this->data)) {
			$this->OpenidSite->updateAllAllowedStates($this->data['OpenidSite'], $session_identity['id']);
		}

		$this->OpenidSite->contain();
		$openidSites = $this->OpenidSite->findAllByIdentityId($session_identity['id']);
		
		if (!empty($openidSites)) {
			$this->set('openidSites', $openidSites);
		} else {
			$this->render('no_openid_sites');
		}			
	}
}