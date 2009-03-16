ALTER TABLE  `networks` ADD  `allow_subscriptions` TINYINT( 1 ) UNSIGNED NOT NULL AFTER  `default_language` ;
ALTER TABLE  `networks` ADD INDEX (  `allow_subscriptions` ) ;