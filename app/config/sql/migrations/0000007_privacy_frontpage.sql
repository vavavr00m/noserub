ALTER TABLE `identities` ADD `frontpage_updates` TINYINT( 1 ) NOT NULL AFTER `hash` ;
ALTER TABLE `identities` ADD INDEX ( `frontpage_updates` ) ;