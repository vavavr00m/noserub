ALTER TABLE `identities` ADD `last_location_id` INT( 11 ) UNSIGNED NOT NULL AFTER `longitude` ;
ALTER TABLE `identities` ADD INDEX ( `last_location_id` ) ;