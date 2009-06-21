<?php
/**
 * Helper for all the widget and functions to be used
 * in NoseRub themes
 */
class NoserubHelper extends AppHelper {
    public $helpers = array('session', 'html');
    
    public function widgetContacts($options = array()) {
        return $this->out('/widgets/contacts_for_identity/', $options);
    }
    
    public function widgetMyContacts() {
        if($this->session->read('Identity.id')) {
            return $this->out('/widgets/my_contacts/');
        } else {
            return $this->out('');
        }
    }
    
    public function widgetNavigation($type = 'meta') {
        if($type != 'meta' && $type != 'main') {
            return $this->out('');
        }
        return $this->out('/widgets/navigation/', array('type' => $type));
    }

    public function widgetNetworkLifestream() {
        return $this->out('/widgets/lifestream/', array('type' => 'network'));
    }
    
    public function widgetSingleLifestream() {
        return $this->out('/widgets/lifestream/', array('type' => 'single'));
    }

    /**
     * generic method for the more simple widgets
     * 
     * TODO: think about adding a whitelist
     */
    public function __call($name, $arguments) {
        $name = str_replace('widget', '', $name);
        $name = Inflector::underscore($name);
        
        return $this->out('/widgets/' . $name, $arguments);
    }    
    
    public function formNetworks() {
        return $this->out('/widgets/form_networks/');
    }
    
    public function formAdminSettings() {
        return $this->out('/widgets/form_admin_settings/');
    }
    
    public function formGroupAdd() {
        return $this->out('/widgets/form_groups_add/');
    }
    
    public function formAccounts() {
        return $this->out('/widgets/form_accounts/');
    }
    
    public function fnAvatarBaseUrl() {
        return Context::read('avatar_base_url');
    }

    public function fnProfilePhotoUrl($size = 'default') {
        if(Context::read('identity')) {
            $identity = Context::read('identity');
        } else {
            $identity = Context::read('logged_in_identity');
        }
        $photo = $identity['photo'];
        if($photo) {
            if(strpos($photo, 'http://') === 0 ||
               strpos($photo, 'https://') === 0) {
                # contains a complete path, eg. from not local identities
                $photo_url = $photo;
            } else {
                if($size == 'small') {
                    $photo .= '-small';
                }
                $photo_url = $this->fnAvatarBaseUrl() . $photo . '.jpg';
            }	                
        } else {
        	App::import('Vendor', 'sex');
        	if($size == 'small') {
        	    $photo_url = Sex::getSmallImageUrl($identity['sex']);
        	} else {
                $photo_url = Sex::getImageUrl($identity['sex']);
            }
        }
        
        return $photo_url;
    }
    
    public function fnSecurityToken() {
        return Context::read('security_token');
    }
    
    public function link($url) {
        switch($url) {
            case '/add/as/contact/':
                return $this->linkAddAsContact();
            
            case '/groups/add/':
                return $this->linkGroupAdd();
                
            default:
                return '';
        }
    }
    
    private function linkAddAsContact() {
        if(Context::read('is_self') || 
           !Context::read('logged_in_identity') ||
           Context::read('is_guest')) {
            return '';
        }
        
        if(Context::read('is_contact')) {
            return __('This is one of your contacts.', true);
        }
        
        return $this->html->link(__('Add as contact', true), '/' . Context::read('identity.local_username') . '/add/as/contact/' . $this->fnSecurityToken(), array('class' => 'button-add-contact'));
    }
    
    private function linkGroupAdd() {
        if(!Context::read('logged_in_identity')) {
            return '';
        }
        
        return $this->html->link(__('Add new group', true), '/groups/add/');
    }
    
    /**
     * wrapper for some functionality we need
     * for every widget
     */
    private function out($action, $data = array()) {        
        $data[] = 'return';
        
        return $this->output($this->requestAction($action, $data));
    }
}