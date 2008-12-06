ALTER TABLE  `favorites` ADD  `created` DATETIME NOT NULL AFTER  `identity_id` ;
ALTER TABLE  `favorites` ADD INDEX (  `created` ) ;