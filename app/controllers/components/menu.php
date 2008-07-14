<?php

class MenuComponent extends Object {
	public $components = array('Session');
    
    public function setViewData($controller) {
        App::import('Model', 'Menu');
        $factory = new MenuFactory();
        
        $controller->set('mainMenu', $factory->getMainMenu($this->getMainMenuOptions($controller)));
        $controller->set('subMenu', $factory->getSubMenu($this->getSubMenuOptions($controller)));
        
        // set some data to be compatible with earlier version of this component
        $controller->set('menu', $this->getCompatibilityData($controller));
	}
	
	private function getCompatibilityData($controller) {
		$mainMenu = '';
        if ($controller->name == 'Contacts' && $controller->action == 'network') {
        	$mainMenu = 'network'; 
        }
        
        $data = array('logged_in' => $this->Session->check('Identity'), 'main' => $mainMenu);
        
        return $data;
	}
	
	private function getFilterOption($controller) {
		$filter = isset($controller->params['filter']) ? $controller->params['filter']   : '';
        $filter = ($filter == '') ? 'all' : $filter;
        
        return array('filter' => $filter);
	}
	
	private function getMainMenuOptions($controller) {
		$menuOptions = array('controller' => $controller->name, 'action' => $controller->action);
        
        if ($this->Session->check('Identity')) {
        	$isLocal = $this->Session->read('Identity.is_local') == '1' ? true : false;
        	$localUsername = $this->Session->read('Identity.local_username');
        	$menuOptions = am($menuOptions, array('is_local' => $isLocal, 'local_username' => $localUsername));
        }
        
        return $menuOptions;
	}
	
	private function getSubMenuOptions($controller) {
		$menuOptions = array('controller' => $controller->name, 'action' => $controller->action);
		$menuOptions = am($menuOptions, $this->getFilterOption($controller));
		
        if ($this->Session->check('Identity') && $this->Session->read('Identity.openid') != '') {
        	$menuOptions = am($menuOptions, array('openid_user' => true));
        }
        
        $localUsername = isset($controller->params['username']) ? $controller->params['username'] : '';
        $menuOptions = am($menuOptions, array('local_username' => $localUsername));
        
		return $menuOptions;
	}
}

?>