<?php
class PagesController extends AppController {
    public $uses = array();
    
    public function display() {
        $this->redirect('/social_stream/', null, true);
    }
    
    public function security_check() {
        $this->set('headline', 'There was a security problem');
    }
}