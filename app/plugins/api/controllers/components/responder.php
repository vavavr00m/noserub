<?php

class ResponderComponent extends Object {
	private $controller = null;
	
	public function startUp($controller) {
		$this->controller = $controller;
	}
	
	public function respondWithNoStatusFound() {
		header("HTTP/1.1 404 Not Found");
	    $this->controller->set('data', array('hash' => array('request' => $this->controller->params['url']['url'], 
	        												 'error' => 'No status found with that ID.')));
	}
	
	public function respondWithUserNotFound() {
		header("HTTP/1.1 404 Not Found");
	    $this->controller->set('data', array('hash' => array('request' => $this->controller->params['url']['url'], 
	        												 'error' => 'Not found')));
	}
}