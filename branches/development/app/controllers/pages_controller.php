<?php
class PagesController extends AppController {
    public $uses = array();
    
    public function home() {
        header('X-XRDS-Location: http://' . $_SERVER['SERVER_NAME'] . $this->webroot . 'pages/yadis.xrdf');    
        Context::setPage('home');
    }
    
    public function display() {
        $this->redirect('/social_stream/');
    }
    
    public function security_check() {
        $this->set('headline', __('There was a security problem', true));
    }
}