<?php foreach($data as $item) { ?>
    <foaf:knows rdf:resource="http://<?php echo $item['WithIdentity']['username']; ?>"/>
<?php } ?>