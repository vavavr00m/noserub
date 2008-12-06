<?php

class OpenidSitesController extends AppController {
	public $uses = array('OpenidSite');
	
	public function index() {
		$this->set('headline', __('OpenID settings', true));
		$identity = $this->Session->read('Identity');
		
		if (!empty($this->data)) {
			$this->OpenidSite->updateAllAllowedStates($this->data['OpenidSite'], $identity['id']);
		}

		$this->OpenidSite->contain();
		$openidSites = $this->OpenidSite->findAllByIdentityId($identity['id']);
		
		if (!empty($openidSites)) {
			$this->set('openidSites', $openidSites);
		} else {
			$this->render('no_openid_sites');
		}			
	}
}