INSERT INTO  `services` (`id` ,`internal_name` ,`name` ,`url` ,`service_type_id` ,`help` ,`icon` ,`has_feed` ,`is_contact` ,`minutes_between_updates` ,`created` ,`modified`) VALUES (73 ,  'Zootool',  'Zootool',  'http://zootool.com',  '2',  'Please enter your Zootool Username.',  'zootool.gif',  '1',  '0',  '30', NOW( ) , NOW( ));
DELETE FROM `services` WHERE id=6;
DELETE FROM `entries` WHERE account_id IN (SELECT id FROM `accounts` WHERE service_id=6);
DELETE FROM `accounts` WHERE service_id=6;
CREATE TABLE `peers` (`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,`name` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,`url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,`disabled` TINYINT( 1 ) UNSIGNED NOT NULL ,`last_sync` DATETIME NOT NULL) ENGINE = MYISAM ;
ALTER TABLE `peers` ADD INDEX ( `last_sync` );
ALTER TABLE  `identities` ADD  `notify_contact` SMALLINT UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '0: disabled; 1: email; 2: im; 3: both' AFTER  `allow_emails`;
ALTER TABLE  `identities` ADD  `notify_comment` SMALLINT UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '0: disabled; 1: email; 2: im; 3:both ' AFTER  `notify_contact`;
ALTER TABLE  `identities` ADD  `notify_favorite` SMALLINT UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '0: disabled; 1: email; 2: im; 3:both' AFTER  `notify_comment`;
UPDATE `identities` SET `notify_contact`=1, `notify_comment`=1, `notify_favorite`=1 WHERE `allow_emails`=2;
ALTER TABLE `identities` CHANGE  `allow_emails`  `allow_emails` TINYINT( 2 ) NOT NULL COMMENT  '0: disabled; 1: contacts; 2: registered users';