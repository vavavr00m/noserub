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
        
	    if($this->context['logged_in_user']) {
    	    $this->render('widget_navigation_' . $type . '_logged_in');
        } else {
            $this->render('widget_navigation_' . $type . '_logged_out');
        }
	}	
}