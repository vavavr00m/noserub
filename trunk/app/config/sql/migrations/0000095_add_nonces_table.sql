CREATE TABLE  `nonces` ( `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , `consumer_id` INT( 11 ) NOT NULL, `token` VARCHAR( 255 ) NOT NULL, `nonce` VARCHAR( 255 ) NOT NULL) ENGINE = MYISAM ;