<?php
class PagesController extends AppController {
    public $uses = array();
    
    public function display() {
        $this->redirect('/social_stream/');
    }
    
    public function security_check() {
        $this->set('headline', __('There was a security problem', true));
    }
    
    public function widget_navigation() {
        $type = isset($this->params['type']) ? $this->params['type'] : 'main';
        
	    if($this->context['logged_in_identity']) {
	        App::import('Model', 'Contact');
	        $Contact = new Contact();
	        
	        $this->set('contact_tags', $Contact->getTagList($this->context['logged_in_identity']['id']));
	        
    	    $this->render('widget_navigation_' . $type . '_logged_in');
        } else {
            $this->render('widget_navigation_' . $type . '_logged_out');
        }
	}	
}