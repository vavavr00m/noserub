ALTER TABLE `identities` ADD `login_hash` CHAR( 32 ) NOT NULL AFTER `security_token` ;
ALTER TABLE `identities` ADD INDEX ( `login_hash` ) ;