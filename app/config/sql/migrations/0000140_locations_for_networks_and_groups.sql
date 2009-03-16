ALTER TABLE  `networks` ADD  `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER  `url` ,ADD  `latitude` FLOAT NOT NULL AFTER  `description` ,ADD  `longitude` FLOAT NOT NULL AFTER  `latitude` ,ADD  `image` VARCHAR( 255 ) NOT NULL AFTER  `longitude` ,ADD  `image_downloaded` DATETIME NOT NULL AFTER  `image` ;
ALTER TABLE  `networks` ADD INDEX (  `latitude` ,  `longitude` ) ;
ALTER TABLE  `networks` ADD  `users` INT NOT NULL AFTER  `image_downloaded` ;
ALTER TABLE  `groups` ADD  `latitude` FLOAT NOT NULL AFTER  `description` ,ADD  `longitude` FLOAT NOT NULL AFTER  `latitude` ;
ALTER TABLE  `groups` ADD INDEX (  `latitude` ,  `longitude` ) ;
ALTER TABLE  `groups` ADD  `image_downloaded` DATETIME NOT NULL AFTER  `image` ;