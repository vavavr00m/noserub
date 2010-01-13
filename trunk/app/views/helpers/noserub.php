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

    public function widgetFlashMessage() {
        $flash_messages = $this->session->read('FlashMessages');
        
        $out = '';
        if($flash_messages) {
            foreach($flash_messages as $type => $messages) {
                $out .= '<div class="message ' . $type . '">';
                foreach($messages as $message) {
                    $out .= '<p>' . $message . '</p>';
                }
                $out .= '</div>';
            }
        }
                
        return $this->output($out);
    }
    
    /**
     * Get group slug by given group_id or
     * model and foreign_key
     */
    public function groupSlug($options) {
        $group_id = false;
        if(isset($options['group_id'])) {
            $group_id = $options['group_id'];
        } else if(isset($options['model']) && 
                  isset($options['foreign_key']) &&
                  $options['model'] == 'group') {
            $group_id = $options['foreign_key'];
        }
        if($group_id) {
            if(!isset($this->Group)) {
                App::import('Model', 'Group');
                $this->Group = new Group();
            }
            $this->Group->id = $group_id;
            return $this->Group->field('slug');
        } else {
            return '';
        }
    }
    
    /**
     * generic method for the more simple widgets
     * 
     * TODO: think about adding a whitelist
     */
    public function __call($name, $arguments) {
        if(strpos($name, 'widget') === 0) {
            $name = str_replace('widget', '', $name);
        } 
        
        $name = Inflector::underscore($name);
        
        return $this->out('/widgets/' . $name, $arguments);
    }    
    
    public function fnAvatarBaseUrl() {
        return Context::read('avatar_base_url');
    }

    public function fnProfilePhotoUrl($size = 'default') {
        if(Context::read('logged_in_identity')) {
            $identity = Context::read('logged_in_identity');
        } else {
            $identity = Context::read('identity');
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
    
    public function fnSecurityTokenInput() {
        return '<input type="hidden" name="security_token" value="' . $this->fnSecurityToken() . '" />';
    }
    
    public function link($url, $data = null) {
        switch($url) {
            case '/groups/add/':
                return $this->linkGroupAdd();
                
            case '/groups/manage_subscription/':
                return $this->linkGroupManageSubscription();
                
            case '/contact/manage/':
                return $this->linkContactManage();
                
            case '/message/send/':
                return $this->linkSendMessage();
            
            case '/entry/toggle/mark/':
                return $this->linkEntryToggleMark($data);
                
            default:
                return '';
        }
    }
    
    private function linkEntryToggleMark($entry_id) {
        if(Context::isLoggedIn()) {
            return $this->html->link(
                '',
                '/entry/mark/' . $entry_id . '/_t:' . $this->fnSecurityToken()
            );
        } else {
            return '';
        }
    }
    
    private function linkSendMessage() {
        if(!Context::isLoggedIn()) {
            return '';
        }
                
        if(Context::allowedSending()) {
            return $this->html->link(
                '<span></span>' . __('Send a message', true), 
                '/messages/add/to:' . Context::read('identity.id'), 
                array('class' => 'button send-message', 'escape' => false)
            );
        }
        
        return '';
    }
    
    private function linkGroupAdd() {
        if(!Context::read('logged_in_identity')) {
            return '';
        }
        
        return $this->html->link(__('Add new group', true), '/groups/add/');
    }
    
    private function linkGroupManageSubscription() {
        $label = false; $url = false;
        
        if(Context::isLoggedIn() && Context::groupSlug()) {
            if(!Context::isSubscribed()) {
                $label = __('Subscribe to this group', true);
                $url = '/groups/subscribe/';
            } else {
                $label = __('Unsubscribe from this group', true);
                $url = '/groups/unsubscribe/';
            }
        }
        
        if($label && $url) {
            return $this->html->link(
                '<span></span>' . $label, 
                $url . Context::groupSlug() . '/_t:' . $this->fnSecurityToken(), 
                array('class' => 'button', 'escape' => false)
            );
        }
        
        return '';
    }
    
    private function linkContactManage() {
        $label = false; $url = false;
        
        if(!Context::isSelf() && Context::isLoggedIn() && 
           Context::read('identity.local_username') && !Context::isGuest()) {
            if(Context::isContact()) {
                $label = __('Remove contact', true);
                $url = $url = '/' . Context::read('identity.local_username') . '/remove/contact/' . $this->fnSecurityToken();
            } else {
                $label = __('Add as contact', true);
                $url = '/' . Context::read('identity.local_username') . '/add/as/contact/' . $this->fnSecurityToken();
            }
        }
        
        if($label && $url) {
            return $this->html->link(
                '<span></span>' . $label,
                $url,
                array(
                    'class' => 'button add-contact',
                    'escape' => false
                )
            );
        }

        return '';
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
