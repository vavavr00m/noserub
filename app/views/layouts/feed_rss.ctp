<?php 
$now = date('D, d M Y H:i:s T');

echo '<?xml version="1.0" encoding="UTF-8"?>'; 

header('Content-Type: text/xml');
?>
<!-- generator="NoseRub" -->
<rss version="2.0" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	>

    <channel>
	    <title>
	        <?php echo sprintf(__('%s - %s Feed', true), Context::read('identity.name'), Context::read('network.name')); ?>
	    </title>
	    <link>http://<?php echo Context::read('identity.username'); ?></link>
	    <description>
	        <?php echo sprintf(__('Feed of %s\'s avctivities on %s', true), Context::read('identity.name'), Context::read('network.name')); ?>
	    </description>
	    <pubDate><?php echo $now; ?></pubDate>
	    <generator><?php echo Context::read('network.url'); ?></generator>
	    <language>en</language>
	    <?php $num_items = 0; ?>
	    <?php foreach($items as $item) { ?>
	        <item>
		        <title><?php echo $item['Entry']['title']; ?></title>
		        <link><?php echo $item['Entry']['url']; ?></link>
		        <pubDate><?php echo date('D, d M Y H:i:s T', strtotime($item['Entry']['published_on'])); ?></pubDate>
		        <dc:creator>http://<?php echo $item['Identity']['username']; ?></dc:creator>
                <description><![CDATA[<?php echo substr($item['Entry']['content'], 0, 255); ?><?php echo strlen($item['Entry']['content']) > 255 ? ' [...]' : ''; ?>]]></description>
                <content:encoded><![CDATA[<?php echo $item['Entry']['content']; ?>]]></content:encoded>
		    </item>
		    <?php 
		        $num_items++;
		        if($num_items > 20) {
		            break;
		        }
		    ?>
        <?php } ?>
	</channel>
</rss>