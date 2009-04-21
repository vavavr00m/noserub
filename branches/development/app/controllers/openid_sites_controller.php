<?php

class OpenidSitesController extends AppController {
	public $uses = array('OpenidSite');
	
	public function beforeFilter() {
		parent::beforeFilter();
		
		if (!Context::isLoggedInIdentity()) {
			$this->redirect('/');
		}
	}
	
	public function index() {
		$this->set('headline', __('OpenID settings', true));
		
		if (!empty($this->data)) {
			$this->OpenidSite->updateAllAllowedStates($this->data['OpenidSite'], Context::loggedInIdentityId());
		}

		$this->OpenidSite->contain();
		$openidSites = $this->OpenidSite->findAllByIdentityId(Context::loggedInIdentityId());
		
		if (!empty($openidSites)) {
			$this->set('openidSites', $openidSites);
		} else {
			$this->render('no_openid_sites');
		}			
	}
}