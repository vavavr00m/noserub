ALTER TABLE `access_tokens` ADD `identity_id` int(11) unsigned NOT NULL AFTER `consumer_id` ;
ALTER TABLE `request_tokens` ADD `identity_id` int(11) unsigned DEFAULT NULL AFTER `consumer_id` ;