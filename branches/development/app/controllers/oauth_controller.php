<?php

class OauthController extends AppController {
	public $uses = array();
	
	public function index() {
		$this->set('headline', 'OAuth');
	}
}
?>