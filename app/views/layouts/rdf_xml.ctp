<?php header("Content-Type:application/rdf+xml"); ?>
<?xml version="1.0" encoding="UTF-8"?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:foaf="http://xmlns.com/foaf/0.1/">
<foaf:Person rdf:about="<?php echo Router::url('/' . Context::read('identity.username'), true); ?>"></foaf:Person>
</rdf:RDF>