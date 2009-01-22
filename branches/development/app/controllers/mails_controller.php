<?php
/* SVN FILE: $Id:$ */
 
class MailsController extends AppController {
    public $components = array('Email');
    
    public function send() {
        if(!isset($this->params['bare']) || 
           !$this->params['bare']) {
            # check that this can only be accessed through
            # requestAction
            return false;
        }
        
        $this->Email->to       = $this->params['to'];
        $this->Email->subject  = $this->params['subject'];
        $this->Email->from     = $this->params['from'];
        $this->Email->template = $this->params['template'];
        $this->Email->sendAs   = 'both';
        
        if(Configure::read('NoseRub.smtp_options')) {
            $this->Email->smtpOptions = Configure::read('NoseRub.smtp_options');
            $this->Email->delivery    = 'smtp';
        }
        
        foreach($this->params['data'] as $key => $value) {
            $this->set($key, $value);
        }
        
        $this->Email->send();
    }
}