<?php

App::import(
    'Vendor', 
    'ContentNegotiation', 
    array('file' => 
        'content_negotiation' . DS . 'content_negotiation.php'
    )
);

class ContentNegotiationComponent extends Object {	
    private $content_negotiation;
    
    public function __construct() {
        $this->content_negotiation = new ContentNegotiation();
    }
    
    public function isApplicationRdfXml() {
        return $this->content_negotiation->compareQ('text/html,application/rdf+xml', 'Accept') == 'application/rdf+xml';
    }
}