ALTER TABLE `identities` ADD `allow_emails` TINYINT( 2 ) NOT NULL AFTER `frontpage_updates` ;
UPDATE `identities` SET `allow_emails` = 2;