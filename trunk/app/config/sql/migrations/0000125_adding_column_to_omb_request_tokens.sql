TRUNCATE `omb_request_tokens`;
ALTER TABLE `omb_request_tokens` ADD `with_identity_id` INT(11) AFTER `identity_id` ;