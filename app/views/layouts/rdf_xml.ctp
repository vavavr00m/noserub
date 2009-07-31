<?php header("Content-Type:application/rdf+xml"); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:foaf="http://xmlns.com/foaf/0.1/">
<foaf:Person rdf:about="<?php echo Router::url('/' . Context::read('identity.username'), true); ?>">
    <foaf:givenname><?php echo Context::read('identity.firstname'); ?></foaf:givenname>
    <foaf:family_name><?php echo Context::read('identity.lastname'); ?></foaf:family_name>
    <?php echo $noserub->widgetContacts(array('layout' => 'rdf')); ?>
</foaf:Person>
</rdf:RDF>