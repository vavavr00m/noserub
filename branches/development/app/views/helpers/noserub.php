<?php
/**
 * Helper for all the widget and functions to be used
 * in NoseRub themes
 */
class NoserubHelper extends AppHelper {
    public $helpers = array('Session');
    
    public function widgetContacts($options = array()) {
        return $this->out('/widgets/contacts_for_identity/', $options);
    }
    
    public function widgetMyContacts() {
        if($this->Session->read('Identity.id')) {
            return $this->out('/widgets/my_contacts/');
        } else {
            return $this->out('');
        }
    }
    
    public function widgetNewestUsers() {
        return $this->out('/widgets/new_users/');
    }
    
    public function widgetContactFilter() {
        return $this->out('/widgets/contact_filter');
    }
    
    public function widgetNavigation($type = 'meta') {
        if($type != 'meta' && $type != 'main') {
            return $this->out('');
        }
        return $this->out('/widgets/navigation/', array('type' => $type));
    }
    
    public function widgetAdminMenu() {
        return $this->out('/widgets/admin_navigation/');
    }
    
    public function widgetAdminLogin() {
        return $this->out('/widgets/admin_login/');
    }
    
    public function widgetNetworkLifestream() {
        return $this->out('/widgets/lifestream/', array('type' => 'network'));
    }
    
    public function widgetSingleLifestream() {
        return $this->out('/widgets/lifestream/', array('type' => 'single'));
    }

    public function widgetGroups() {
        return $this->out('/widgets/groups/');
    }
    
    public function widgetNetworks() {
        return $this->out('/widgets/networks/');
    }
    
    public function formNetworks() {
        return $this->out('/widgets/form_networks/');
    }
    
    public function fnAvatarBaseUrl() {
        $url = '';
    	
    	if(Configure::read('NoseRub.use_cdn')) {
            $url = 'http://s3.amazonaws.com/' . Configure::read('NoseRub.cdn_s3_bucket') . '/avatars/';
        } else {
            $url = Router::url('/static/avatars/', true);
        }
        
        return $url;
    }

    /**
     * wrapper for some functionality we need
     * for every widget
     */
    private function out($action, $data = array()) {        
        $data[] = 'return';
        
        $View = ClassRegistry::getObject('view');
        $data['context'] = $View->viewVars['context'];

        return $this->output($this->requestAction($action, $data));
    }
}