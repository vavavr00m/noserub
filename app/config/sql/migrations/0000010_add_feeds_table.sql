CREATE TABLE `feeds` (`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `account_id` INT( 11 ) UNSIGNED NOT NULL, `content` LONGTEXT NOT NULL, `priority` MEDIUMINT UNSIGNED NOT NULL, `created` DATETIME NOT NULL, `modified` DATETIME NOT NULL) ENGINE = MYISAM;
ALTER TABLE `feeds` ADD UNIQUE (`account_id`);
ALTER TABLE `feeds` ADD INDEX (`priority`);
ALTER TABLE `feeds` ADD INDEX (`modified`);  