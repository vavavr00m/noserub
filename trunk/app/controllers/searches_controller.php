<?php

class SearchesController extends AppController {
    public $uses = array('Entry');
    public $helpers = array('nicetime');
    public $components = array('cluster');
    
    /**
     * 
     */
    public function index() {
        $this->checkUnsecure();
    }    
}