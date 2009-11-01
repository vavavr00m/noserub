ALTER TABLE `syndications` ADD `last_upload` DATETIME NOT NULL AFTER `hash`;
ALTER TABLE `syndications` ADD INDEX ( `last_upload` );
ALTER TABLE `syndications` ADD INDEX ( `hash` );    