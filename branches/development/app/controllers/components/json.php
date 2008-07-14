<?php

App::import('Vendor', 'json', array('file' => 'Zend'.DS.'Json.php'));

class JsonComponent extends Object {

    private $zend_json = null;
     
    public function __construct() {
        $this->zend_json = new Zend_Json();
        $this->zend_json->useBuiltinEncoderDecoder = true;
    }
    
    public function encode($value) {
        return $this->zend_json->encode($value);
    }
    
    public function decode($value) {
        return $this->zend_json->decode($value);
    }
}

?>