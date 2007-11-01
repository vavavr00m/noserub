<?php

	class OpenidSitesController extends AppController {
		var $helpers = array('form');
		var $uses = array('OpenidSite');
		
		function index() {
			$this->set('headline', 'OpenID settings');
			$identity = $this->Session->read('Identity');
			
			if (!empty($this->data)) {
				$this->OpenidSite->updateAllAllowedStates($this->data['OpenidSite'], $identity['id']);
			}
			$this->OpenidSite->expects('OpenidSite');
			$openidSites = $this->OpenidSite->findAllByIdentityId($identity['id']);
			
			if (!empty($openidSites)) {
				$this->set('openidSites', $openidSites);
			} else {
				$this->render('no_openid_sites');
			}			
		}
	}
?>