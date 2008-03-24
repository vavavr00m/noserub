ALTER TABLE `feeds` DROP `priority`;
ALTER TABLE  `feeds` ADD  `updated` DATETIME NOT NULL AFTER  `date_newest_item` ;
ALTER TABLE  `feeds` ADD INDEX (  `updated` ) ;
UPDATE accounts SET feed_url = REPLACE(feed_url, 'http://del.icio.us/rss/', 'http://feeds.delicious.com/rss/') WHERE feed_url LIKE '%del.icio.us%';