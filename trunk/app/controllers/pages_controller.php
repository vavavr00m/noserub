<?php
class PagesController extends AppController {
    var $uses = array();
    
    function display() {
        $this->redirect('/social_stream/', null, true);
    }
}