CREATE TABLE  `locations` ( `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , `name` VARCHAR( 64 ) NOT NULL , `address` VARCHAR( 128 ) NOT NULL , `latitude` FLOAT NOT NULL , `longitude` DATETIME NOT NULL , `created` DATETIME NOT NULL , `modified` DATETIME NOT NULL) ENGINE = MYISAM ;
ALTER TABLE  `locations` ADD  `identity_id` INT( 11 ) UNSIGNED NOT NULL AFTER  `id` ;
ALTER TABLE  `locations` ADD INDEX (  `identity_id` ) ;
CREATE TABLE `activities` (`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `identity_id` INT( 11 ) NOT NULL, `service_type_id` INT( 11 ) UNSIGNED NOT NULL, `content` VARCHAR( 255 ) NOT NULL, `created` DATETIME NOT NULL, `modified` DATETIME NOT NULL) ENGINE = MYISAM ;
 ALTER TABLE `activities` ADD INDEX ( `identity_id` , `service_type_id` );
 INSERT INTO `service_types` (`id`, `token`, `name`, `intro`, `created`, `modified`) VALUES (0, 'noserub', 'NoseRub', '@user@ @item@', NOW(), NOW());
 UPDATE `service_types` SET id=0 WHERE token='noserub';