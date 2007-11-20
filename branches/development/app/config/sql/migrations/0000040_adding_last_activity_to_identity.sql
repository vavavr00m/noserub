ALTER TABLE `identities` ADD `last_activity` DATETIME NOT NULL AFTER `frontpage_updates` ;
ALTER TABLE `identities` ADD INDEX ( `last_activity` ) ;