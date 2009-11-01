<?php

class ResponderComponent extends Object {
	private $controller = null;
	
	public function startUp($controller) {
		$this->controller = $controller;
	}
	
	public function respondWithNoStatusFound() {
		$this->respondWith404('No status found with that ID.');
	}
	
	public function respondWithNotAuthorized() {
		$this->respondWith401('Could not authenticate you.');
	}
	
	public function respondWithUserNotFound() {
		$this->respondWith404('Not found');
	}
	
	private function respondWith401($error_message) {
		header('WWW-Authenticate: Basic realm="NoseRub API"');
		$this->respondWithStatus('401 Unauthorized', $error_message);
	}
	
	private function respondWith404($error_message) {
		$this->respondWithStatus('404 Not Found', $error_message);
	}
	
	private function respondWithStatus($status, $error_message) {
		header("HTTP/1.1 " . $status);
	    $this->controller->set('data', array('hash' => array('request' => $this->controller->params['url']['url'], 
	        												 'error' => $error_message)));
	}
}