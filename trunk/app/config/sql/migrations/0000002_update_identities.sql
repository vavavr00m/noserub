ALTER TABLE `identities` ADD `is_local` TINYINT( 1 ) NOT NULL AFTER `id` ;
ALTER TABLE `identities` ADD INDEX ( `is_local` ) ;