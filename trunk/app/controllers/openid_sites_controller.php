<?php

	class OpenidSitesController extends AppController {
		
		function index() {
			$this->set('headline', 'OpenID settings');
			
			$identity = $this->Session->read('Identity');
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