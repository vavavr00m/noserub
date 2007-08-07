<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
         xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#" 
         xmlns:foaf="http://xmlns.com/foaf/0.1/" 
         xmlns:rel="http://purl.org/vocab/relationship/" 
         xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#">

<?php foreach($data['Account'] as $account) { ?>
    <?php if($account['service_id'] == 7) { ?>
        <foaf:weblog rdf:resource="<?php echo $account['account_url']; ?>"/>
    <?php } else { ?>
        <foaf:holdsAccount>
            <foaf:OnlineAccount rdf:about="<?php echo $account['account_url']; ?>" />
            <foaf:accountServiceHomepage rdf:resource="<?php echo $account['Service']['url']; ?>"/>
            <foaf:accountName><?php echo $account['username']; ?></foaf:accountName>
        </foaf:holdsAccount>
    <?php } ?>
<?php } ?>
</rdf:RDF>	

<?php
/*
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#" xmlns:foaf="http://xmlns.com/foaf/0.1/" xmlns:rel="http://purl.org/vocab/relationship/" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#">

<foaf:Person rdf:nodeID="olbertz-de">
    <foaf:name>Dirk Olbertz</foaf:name>
    <foaf:firstName>Dirk</foaf:firstName>
    <foaf:surname>Olbertz</foaf:surname>
    <foaf:weblog rdf:resource="http://www.olbertz.de/"/>
    <foaf:based_near>
        <geo:Point>
            <geo:lat>50.741788500586</geo:lat>
            <geo:long>7.0946654677391</geo:long>
        </geo:Point>
    </foaf:based_near>
    <foaf:holdsAccount>
        <foaf:OnlineAccount rdf:about="http://blogscout.de/info/profil/olbertz-de">
            <foaf:accountServiceHomepage rdf:resource="http://blogscout.de/"/>
            <foaf:accountName>
                olbertz-de
            </foaf:accountName>
        </foaf:OnlineAccount>
        </foaf:holdsAccount>
        <foaf:holdsAccount>
            <foaf:OnlineAccount rdf:about="http://flickr.com/photos/dirkolbertz/">
                <foaf:accountServiceHomepage rdf:resource="http://flickr.com/"/>
                <foaf:accountName>
                    dirkolbertz
                </foaf:accountName>
            </foaf:OnlineAccount>
        </foaf:holdsAccount>
        <foaf:holdsAccount><foaf:OnlineAccount rdf:about="http://sevenload.de/mitglieder/dirk_olbertz"><foaf:accountServiceHomepage rdf:resource="http://sevenload.de/"/><foaf:accountName>dirk_olbertz</foaf:accountName></foaf:OnlineAccount></foaf:holdsAccount><foaf:holdsAccount><foaf:OnlineAccount rdf:about="http://del.icio.us/dirk_olbertz"><foaf:accountServiceHomepage rdf:resource="http://del.icio.us/"/><foaf:accountName>dirk_olbertz</foaf:accountName></foaf:OnlineAccount></foaf:holdsAccount><foaf:holdsAccount><foaf:OnlineAccount rdf:about="http://last.fm/user/dirkolbertz/"><foaf:accountServiceHomepage rdf:resource="http://last.fm/"/><foaf:accountName>dirkolbertz</foaf:accountName></foaf:OnlineAccount></foaf:holdsAccount><foaf:holdsAccount><foaf:OnlineAccount rdf:about="http://youtube.com/profile?user=dirkolbertz"><foaf:accountServiceHomepage rdf:resource="http://youtube.com/"/><foaf:accountName>dirkolbertz</foaf:accountName></foaf:OnlineAccount></foaf:holdsAccount><foaf:holdsAccount><foaf:OnlineAccount rdf:about="http://beta.plazes.com/user/dirkolbertz"><foaf:accountServiceHomepage rdf:resource="http://plazes.com/"/><foaf:accountName>dirkolbertz</foaf:accountName></foaf:OnlineAccount></foaf:holdsAccount><foaf:knows><foaf:Person><foaf:name>BasicThinking</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/basicthinking"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>blog.blogscout.de</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/blog.blogscout.de"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>Nuf</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/dasnuf"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>Irgendwas ist ja immer</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/DonDahlmann"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>Sanníe</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/elfengleich"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>fscklog</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/fscklog"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>lawblog</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/lawblog"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>Das meiersonlineblog aus Frankfurt am Main</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/meiersonlineblog.de"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>miagolare. die hafenversion</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/miagolare"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>Ole, Schorsch, Stefan, Jan,Thorsten, Enrico ... MP:Team</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/MP.Blog"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>Alexander Kaiser</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/poolie"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>popkulturjunkie.de</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/popkulturjunkie"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>powerbook blog</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/powerbook-blogger"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>Spreeblick</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/spreeblick"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>Technorat</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/technorat"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>Timo Derstappen</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/teemow"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>/usr/portage</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/usrportage"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>Felix Schwenzel</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/wirres"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>Stefan Niggemeier</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/stefan-niggemeier.de"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>Thomas Knüwer</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/Indiskretion"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>Kai Pahl</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/allesaussersport.de"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>blogbar</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/blogbar.de"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>Kai Pahl</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/kaipahl.de"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>karsten.feednet.de</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/karsten.feednet.de"/></foaf:Person></foaf:knows><foaf:knows><foaf:Person><foaf:name>Julia Janßen</foaf:name><rdfs:seeAlso rdf:resource="http://foaf.blogscout.de/juliajanssen.de"/></foaf:Person></foaf:knows></foaf:Person></rdf:RDF>
*/
?>