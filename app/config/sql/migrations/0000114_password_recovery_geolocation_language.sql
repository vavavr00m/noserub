ALTER TABLE `identities` ADD `password_recovery_hash` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `hash` ;
ALTER TABLE `identities` ADD INDEX ( `password_recovery_hash` ) ;
ALTER TABLE `identities` ADD `language` CHAR( 8 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `about` ;
ALTER TABLE `entries` ADD `latitude` FLOAT NULL AFTER `content` , ADD `longitude` FLOAT NULL AFTER `latitude` ;
ALTER TABLE `entries` ADD INDEX ( `latitude` , `longitude` ) ;