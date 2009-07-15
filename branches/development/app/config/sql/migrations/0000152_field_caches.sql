ALTER TABLE `groups` ADD `subscriber_count` INT( 11 ) UNSIGNED NOT NULL AFTER `entry_count` ;
ALTER TABLE `groups` ADD `last_activity` DATETIME NOT NULL AFTER `subscriber_count` ;
ALTER TABLE `groups` ADD INDEX ( `last_activity` ) ;
ALTER TABLE `entries` ADD `last_activity` DATETIME NOT NULL AFTER `favorite_count` ;
ALTER TABLE `entries` ADD INDEX ( `last_activity` ) ;