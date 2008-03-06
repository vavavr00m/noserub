<?php

class ApiComponent extends Object {
    private $controller = null;
	
	public function startUp($controller) {
		$this->controller = $controller;
	}
	
    public function exitWith404ErrorIfInvalid($identity) {
    	$api_hash = isset($this->controller->params['api_hash']) ? $this->controller->params['api_hash'] : '';
    	
    	if($identity['Identity']['api_hash'] != $api_hash || $identity['Identity']['api_active'] == false) {
        	$this->controller->cakeError('error404', array(array('action' => $this->controller->here)));
        }
    }
    
    public function getIdentity() {
        if(!isset($this->controller->Identity)) {
            App::import('Model', 'Identity');
            $this->controller->Identity = new Identity();
        }
        
    	$username = isset($this->controller->params['username']) ? $this->controller->params['username'] : '';
    	$splitted = $this->controller->Identity->splitUsername($username);
    	
    	$this->controller->Identity->recursive = 0;
        $this->controller->Identity->expects('Identity');
        $identity = $this->controller->Identity->findByUsername($splitted['username'], array('id', 'api_hash', 'api_active'));

        return $identity;
    }
    
    public function render() {
    	$result_type = isset($this->controller->params['result_type']) ? $this->controller->params['result_type'] : '';
    	$this->controller->layout = 'api_' . $result_type;
        $this->controller->render('../empty');
    }
}