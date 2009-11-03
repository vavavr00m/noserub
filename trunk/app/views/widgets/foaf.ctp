<?php
    # this is probably no valid FOAF!
    # in order to be able to just store simple RSS-Feeds (service = 'RSS-Feed'),
    # I just leave empty foaf:accountName and use foaf:OnlineAccount and
    # foaf:accountServiceHomepage to store feed_url and account_url    
    
$services = Configure::read('services.data');
?>
<!--
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
         xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#" 
         xmlns:foaf="http://xmlns.com/foaf/0.1/" 
         xmlns:rel="http://purl.org/vocab/relationship/" 
         xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#">

<foaf:Person rdf:nodeID="<?php echo $data['Identity']['username']; ?>">
<?php if($data['Identity']['firstname'] != '') { ?>
    <foaf:firstname><?php echo $data['Identity']['firstname']; ?></foaf:firstname>
<?php } ?>
<?php if($data['Identity']['lastname'] != '') { ?>
    <foaf:surname><?php echo $data['Identity']['lastname']; ?></foaf:surname>
<?php } ?>
<?php if($data['Identity']['sex'] > 0) { ?>
    <foaf:gender><?php echo $data['Identity']['sex'] == 1 ? 'female' : 'male'; ?></foaf:gender>
<?php } ?>
<?php if($data['Identity']['photo'] != '') { ?>
    <foaf:img><?php echo $noserub->fnAvatarBaseUrl() . $data['Identity']['photo']; ?>.jpg</foaf:img>
<?php } ?>
<?php if($data['Identity']['latitude'] != 0 && $data['Identity']['longitude']) { ?>
    <foaf:based_near>
	    <geo:Point>
            <geo:lat><?php echo $data['Identity']['latitude']; ?></geo:lat>
            <geo:long><?php echo $data['Identity']['longitude']; ?></geo:long>
        </geo:Point>
    </foaf:based_near>
<?php } ?>
<?php if($data['Identity']['address_shown'] != '') { ?>
    <foaf:address><?php echo $data['Identity']['address_shown']; ?></foaf:address>
<?php } ?>
<?php if($data['Identity']['about'] != '') { ?>
    <foaf:about><![CDATA[<?php echo htmlentities($data['Identity']['about'], ENT_COMPAT, 'UTF-8'); ?>]]></foaf:about>
<?php } ?>
<?php foreach($data['Account'] as $account) { ?>
    <foaf:holdsAccount>
        <?php if($account['service'] == 'RSS-Feed') { ?>
            <foaf:OnlineAccount rdf:about="<?php echo $account['account_url']; ?>" />
            <foaf:accountServiceHomepage rdf:resource="NoseRubServiceType:<?php echo $account['service_type']; ?>"/>
            <foaf:accountName><?php echo $account['feed_url']; ?></foaf:accountName>
        <?php } else { ?>
            <foaf:OnlineAccount rdf:about="<?php echo $account['account_url']; ?>" />
            <foaf:accountServiceHomepage rdf:resource="<?php echo $services[$account['service']]['url']; ?>"/>
            <foaf:accountName><?php echo $account['username']; ?></foaf:accountName>
        <?php } ?>
    </foaf:holdsAccount>
<?php } ?>
</foaf:Person>
</rdf:RDF>	
-->