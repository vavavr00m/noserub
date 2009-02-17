<?php
/**
 * Helper for all the widget and functions to be used
 * in NoseRub themes
 */
class NoserubHelper extends AppHelper {
    public $helpers = array('Session');
    
    public function widgetContacts($options) {
        $options[] = 'return';
        return $this->output($this->requestAction('/widgets/contacts_for_identity/', $options));
    }
    
    public function widgetMyContacts() {
        if($this->Session->read('Identity.id')) {
            return $this->output($this->requestAction('/widgets/my_contacts/', array('return')));
        } else {
            return $this->output('');
        }
    }
    
    public function widgetNewestUsers() {
        return $this->output($this->requestAction('/widgets/new_users/', array('return')));
    }
    
    public function widgetNavigation($type = 'meta') {
        if($type != 'meta' && $type != 'main') {
            return $this->output('');
        }
        return $this->output($this->requestAction('/widgets/navigation/', array('return', 'type' => $type)));
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
}