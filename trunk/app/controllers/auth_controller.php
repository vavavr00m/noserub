<?php

	class AuthController extends AppController {
		var $uses = array();
		
		function index() {
			echo 'Not yet implemented';
			exit();
		}
		
		function xrds() {
			$this->layout = 'xml';
			header('Content-type: application/xrds+xml');
			$this->set('server', Router::url('/'.low($this->name), true));
		}
	}
?>