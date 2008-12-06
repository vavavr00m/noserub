ALTER TABLE `entries` ADD `uid` CHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `id` ;
ALTER TABLE `entries` ADD INDEX ( `uid` ) ;
UPDATE `entries` SET `uid`=MD5(`url`);
CREATE TABLE `comments` (`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,`entry_id` INT( 11 ) UNSIGNED NOT NULL ,`identity_id` INT( 11 ) UNSIGNED NOT NULL ,`title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,`content` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,`published_on` DATETIME NOT NULL) ENGINE = MYISAM ;
ALTER TABLE `comments` ADD INDEX ( `entry_id` );
ALTER TABLE `comments` ADD INDEX ( `identity_id` );
CREATE TABLE `favorites` (`entry_id` INT( 11 ) UNSIGNED NOT NULL ,`identity_id` INT( 11 ) UNSIGNED NOT NULL ,INDEX ( `entry_id` , `identity_id` )) ENGINE = MYISAM ;