<?php

vendor('Zend/Json');

class JsonComponent extends Object {

    private $zend_json = null;
     
    public function __construct() {
        $this->zend_json = new Zend_Json();
        $this->zend_json->useBuiltinEncoderDecoder = true;
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    public function encode($value) {
        return $this->zend_json->encode($value);
    }
    
    /**
     * Method description
     *
     * @param  
     * @return 
     * @access 
     */
    public function decode($value) {
        return $this->zend_json->decode($value);
    }
    
}

?>