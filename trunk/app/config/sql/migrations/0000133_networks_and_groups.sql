ALTER TABLE `networks` DROP `manual_feeds_update`;
ALTER TABLE `networks` ADD  `registration_restricted_hosts` TEXT NOT NULL AFTER  `registration_type` ;
ALTER TABLE `networks` ADD  `is_local` TINYINT UNSIGNED NOT NULL AFTER  `api_info_active` ;
ALTER TABLE `networks` ADD INDEX (  `is_local` ) ;
UPDATE `networks` SET `is_local` = 1;
ALTER TABLE `networks` ADD  `disabled` TINYINT NOT NULL AFTER  `api_info_active` ;
ALTER TABLE `networks` ADD INDEX (  `disabled` ) ;
ALTER TABLE `networks` ADD  `last_sync` DATETIME NOT NULL AFTER  `is_local` ;
ALTER TABLE `networks` ADD INDEX (  `last_sync` ) ;
INSERT INTO `networks` (name, url, disabled, last_sync, is_local, modified, created) SELECT name, url, disabled, last_sync, 0, NOW(), NOW() FROM `peers`;
DROP TABLE `peers`;
CREATE TABLE `groups` (`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,`network_id` INT( 11 ) UNSIGNED NOT NULL ,`name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,`image` VARCHAR( 255 ) NOT NULL ,`slug` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,`modified` DATETIME NOT NULL ,`created` DATETIME NOT NULL ,PRIMARY KEY (  `id` ) ,INDEX (  `network_id` ,  `slug` )) ENGINE = MYISAM;
CREATE TABLE `group_subscriptions` (`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,`identity_id` INT( 11 ) UNSIGNED NOT NULL ,`group_id` INT( 11 ) UNSIGNED NOT NULL ,PRIMARY KEY (  `id` ) ,INDEX (  `identity_id` ,  `group_id` )) ENGINE = MYISAM;
CREATE TABLE `group_admins` (`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,`identity_id` INT( 11 ) UNSIGNED NOT NULL ,`group_id` INT( 11 ) UNSIGNED NOT NULL ,PRIMARY KEY (  `id` ) ,INDEX (  `identity_id` ,  `group_id` )) ENGINE = MYISAM;
ALTER TABLE `entries` ADD  `group_id` INT( 11 ) UNSIGNED NOT NULL COMMENT  'if 0, this is an entry from an account' AFTER  `identity_id` ;
ALTER TABLE `entries` ADD INDEX (  `group_id` ) ;
ALTER TABLE `entries` CHANGE  `account_id`  `account_id` INT( 11 ) UNSIGNED NOT NULL COMMENT  'if 0, this is an entry from a group';