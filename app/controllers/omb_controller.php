<?php

class OmbController extends AppController {
	public $uses = array();
	
	public function index() {
		$this->set('headline', 'OpenMicroBlogging');
	}
}