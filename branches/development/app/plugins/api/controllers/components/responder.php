<?php

class ResponderComponent extends Object {
	private $controller = null;
	
	public function startUp($controller) {
		$this->controller = $controller;
	}
	
	public function respondWithNoStatusFound() {
		$this->respondWith404('No status found with that ID.');
	}
	
	public function respondWithUserNotFound() {
		$this->respondWith404('Not found');
	}
	
	private function respondWith404($error_message) {
		header("HTTP/1.1 404 Not Found");
	    $this->controller->set('data', array('hash' => array('request' => $this->controller->params['url']['url'], 
	        												 'error' => $error_message)));
	}
}