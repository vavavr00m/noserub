ALTER TABLE  `locations` ADD  `uid` CHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER  `id` ;
ALTER TABLE  `locations` ADD INDEX (  `uid` ) ;
ALTER TABLE  `events` ADD  `uid` CHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER  `id` ;
ALTER TABLE  `events` ADD INDEX (  `uid` ) ;
CREATE TABLE  `ads` (`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,`network_id` INT( 11 ) UNSIGNED NOT NULL ,`name` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,`width` SMALLINT UNSIGNED NOT NULL ,`height` SMALLINT UNSIGNED NOT NULL ,`content` TEXT NOT NULL ,`allow_php` TINYINT UNSIGNED NOT NULL ,`created` DATETIME NOT NULL) ENGINE = MYISAM;
ALTER TABLE  `ads` ADD INDEX (  `network_id` );