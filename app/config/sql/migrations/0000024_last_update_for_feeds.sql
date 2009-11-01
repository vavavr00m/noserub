ALTER TABLE `feeds` ADD `date_newest_item` DATETIME NOT NULL AFTER `content` ;
ALTER TABLE `feeds` ADD INDEX ( `date_newest_item` ) ;