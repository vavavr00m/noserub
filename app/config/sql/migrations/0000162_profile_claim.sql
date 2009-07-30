ALTER TABLE `identities` ADD `title` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `lastname` ;
ALTER TABLE `identities` ADD `keywords` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `title` ;
ALTER TABLE `identities` ADD `status` SMALLINT UNSIGNED NOT NULL AFTER `last_login` ;
ALTER TABLE `identities` ADD INDEX ( `status` ) ;
ALTER TABLE `identities` ADD `tracking_codes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Holds base64 encoded serialized php array with information about tracking codes like sitemeter and analytics' AFTER `keywords` ;
ALTER TABLE `networks` ADD `tracking_codes` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Holds base64 encoded serialized php array with information about tracking codes like sitemeter and analytics' AFTER `description` ;
ALTER TABLE `accounts` ADD `max_lifetime_days` SMALLINT UNSIGNED NOT NULL COMMENT 'After how many days should items from this account be deleted? 0 means never (or network setting)' AFTER `next_update` ;
ALTER TABLE `accounts` ADD `claimed` TINYINT UNSIGNED NOT NULL AFTER `feed_url`, ADD `claim_hash` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `claimed` ;