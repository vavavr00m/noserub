<?php

class MenuComponent extends Object {

    private $controller;
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    function init(&$controller) {
        
    }
    
    function setViewData(&$controller, $model = null, $action = null) {
	    $model  = $model  === null ? $controller->name   : $model;
        $action = $action === null ? $controller->action : $action;

        $filter    = isset($controller->params['filter'])   ? $controller->params['filter']   : '';
        $logged_in = isset($_SESSION['Identity']);
        
        $main_menu = '';
        $sub_menu  = '';
        switch($model) {
            case 'Accounts':
                $main_menu = 'settings';
                $sub_menu = 'accounts';
                break;

            case 'Contacts':
                switch($action) {
                    case 'network':
                        $main_menu = 'network';
                        $sub_menu  = $filter == '' ? 'all' : $filter;
                        break;

                    default:
                        $main_menu = 'my_contacts'; break;
                }
                break;
                
            case 'Identities':
                switch($action) {
                    case 'register':
                        $main_menu = 'register'; break;
                        
                    case 'index':
                        $main_menu = 'my_profile'; 
                        $sub_menu  = $filter == '' ? 'all' : $filter;
                        break;
                        
                    case 'profile_settings':
                        $main_menu = 'settings'; 
                        $sub_menu  = 'profile'; 
                        break;
                        
                    case 'privacy_settings':
                        $main_menu = 'settings'; 
                        $sub_menu  = 'privacy'; 
                        break;
                        
                    case 'password_settings':
                        $main_menu = 'settings'; 
                        $sub_menu  = 'password'; 
                        break;
                        
                    case 'account_settings':
                        $main_menu = 'settings';
                        $sub_menu  = 'account';
                        break;
                }
                break;
            
			case 'OpenidSites':
				$main_menu = 'settings'; 
                $sub_menu  = 'openid';
                break;
				
            case 'Pages':
                switch($action) {
                    case 'display':
                    case 'home':
                        $main_menu = 'social_stream'; break;
                }
                break;
                
            case 'Syndications':
                $main_menu = 'settings';
                $sub_menu  = 'feeds';
                break;
        }

        $data = array('main' => $main_menu,
                      'sub'  => $sub_menu,
                      'model'     => $model,
                      'action'    => $action,
                      'filter'    => $filter,
                      'logged_in' => $logged_in);
                      
        $controller->set('menu', $data);
	}
}

?>