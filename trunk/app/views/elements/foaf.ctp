<?php
    # this is probably no valid FOAF!
    # in order to be able to just store simple RSS-Feeds (service_id = 8),
    # I just leave empty foaf:accountName and use foaf:OnlineAccount and
    # foaf:accountServiceHomepage to store feed_url and account_url
?>
<!--
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
         xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#" 
         xmlns:foaf="http://xmlns.com/foaf/0.1/" 
         xmlns:rel="http://purl.org/vocab/relationship/" 
         xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#">

<?php foreach($data['Account'] as $account) { ?>
    <foaf:holdsAccount>
        <?php if($account['service_id'] == 8) { ?>
            <foaf:OnlineAccount rdf:about="<?php echo $account['account_url']; ?>" />
            <foaf:accountServiceHomepage rdf:resource="NoseRubServiceType:<?php echo $account['service_type_id']; ?>"/>
            <foaf:accountName rdf:resource="<?php echo $account['feed_url']; ?>"/>
        <?php } else { ?>
            <foaf:OnlineAccount rdf:about="<?php echo $account['account_url']; ?>" />
            <foaf:accountServiceHomepage rdf:resource="<?php echo $account['Service']['url']; ?>"/>
            <foaf:accountName><?php echo $account['username']; ?></foaf:accountName>
        <?php } ?>
    </foaf:holdsAccount>
<?php } ?>
</rdf:RDF>	
-->