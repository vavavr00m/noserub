<?php
class LocationsController extends AppController {
    var $uses = array('Location');
    
    public function index() {
        $this->set('headline', 'Your locations');
    }
}