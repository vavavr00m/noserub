<?php
/**
 * Helper for all the widget and functions to be used
 * in NoseRub themes
 */
class NoserubHelper extends AppHelper {
    public $helpers = array('Session');
    
    public function widgetContacts($options) {
        $options[] = 'return';
        return $this->output($this->requestAction('/widget/contacts/', $options));
    }
    
    public function widgetMyContacts() {
        if($this->Session->read('Identity.id')) {
            return $this->output($this->requestAction('/widget/contacts/my/', array('return')));
        } else {
            return $this->output('');
        }
    }
    
    public function widgetNewestUsers() {
        return $this->output($this->requestAction('/widget/users/new/', array('return')));
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