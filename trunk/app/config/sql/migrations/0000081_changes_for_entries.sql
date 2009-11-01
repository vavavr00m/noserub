ALTER TABLE `services` ADD `minutes_between_updates` SMALLINT UNSIGNED NOT NULL AFTER `is_contact` ;
UPDATE `services` SET minutes_between_updates=30 WHERE has_feed=1 AND is_contact=0;
UPDATE `services` SET minutes_between_updates=5 WHERE id IN (5, 6, 11);
ALTER TABLE `accounts` ADD `next_update` DATETIME NOT NULL AFTER `feed_url` ;
ALTER TABLE `accounts` ADD INDEX ( `next_update` ) ;
DROP TABLE `feeds`;
DROP TABLE `activities`;