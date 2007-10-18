<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!-- generator="NoseRub" -->
<rss version="2.0" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	>

    <channel>
	    <title><?php echo $syndication_name; ?> - NoseRub Feed</title>
	    <link>http://<?php echo $identity['username']; ?></link>
	    <description><?php echo $syndication_name; ?> - NoseRub Feed</description>
	    <pubDate>Mon, 17 Sep 2007 20:14:49 +0000</pubDate>
	    <generator>http://noserub.com/</generator>
	    <language>en</language>
	    <?php foreach($data as $item) { ?>
	        <item>
		        <title><?php echo $item['title']; ?></title>
		        <link><?php echo $item['url']; ?></link>
		        <pubDate>Mon, 17 Sep 2007 20:14:49 +0000</pubDate>
		        <dc:creator>http://<?php echo $item['username']; ?></dc:creator>
                <description><![CDATA[<?php echo substr(strip_tags($item['content']), 0, 255); ?> [...]]]></description>
                <content:encoded><![CDATA[<?php echo strip_tags($item['content']); ?>]]></content:encoded>
		    </item>
        <?php } ?>
	</channel>
</rss>
