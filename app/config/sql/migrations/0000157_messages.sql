CREATE TABLE  `messages` (`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , `identity_id` INT( 11 ) UNSIGNED NOT NULL , `folder` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , `to_from` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , `subject` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , `text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , `read` TINYINT NOT NULL , `replied` TINYINT NOT NULL , `created` DATETIME NOT NULL);
ALTER TABLE  `messages` ADD INDEX (  `identity_id` );
ALTER TABLE  `messages` ADD INDEX (  `folder` );
ALTER TABLE  `messages` ADD INDEX (  `created` );
ALTER TABLE  `identities` ADD  `message_count` MEDIUMINT UNSIGNED NOT NULL COMMENT  'counter cache' AFTER  `security_token;