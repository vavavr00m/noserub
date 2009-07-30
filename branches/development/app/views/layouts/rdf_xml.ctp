<?php header("Content-Type:application/rdf+xml"); ?>
<foaf:Person rdf:about="<?php echo Router::url('/' . Context::read('identity.username'), true); ?>"></foaf:Person>
