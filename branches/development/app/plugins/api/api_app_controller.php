<?php

class ApiAppController extends AppController {
	public $components = array('Api');
	
	public function beforeRender() {
		$pathToViewFile = dirname(__FILE__).DS.'views'.DS.$this->viewPath.DS.$this->action.'.ctp';
		
		if (!file_exists($pathToViewFile)) {
			$this->viewPath = $this->layoutPath;
			$this->action = 'default';
		}
	}
}